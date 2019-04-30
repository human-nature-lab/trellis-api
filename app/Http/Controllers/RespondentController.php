<?php
namespace App\Http\Controllers;

use App\Models\Geo;
use App\Models\Respondent;
use App\Models\Photo;
use App\Models\RespondentFill;
use App\Models\RespondentName;
use App\Models\Study;
use App\Models\RespondentPhoto;
use App\Models\StudyRespondent;
use App\Services\PreloadActionService;
use App\Services\RespondentPhotoService;
use App\Services\RespondentService;
use \DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Input;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDOException;
use Throwable;
use Validator;
use Ramsey\Uuid\Uuid;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Csv\Reader;

class RespondentController extends Controller
{
    public function index()
    {
        $respondents = Respondent::where('deleted_at', null)
            ->orderBy('created_at')
            ->paginate(20);

        return view('respondents.respondents', array('title' => 'Respondents', 'respondents' => $respondents));
    }

    /**
     * Get all surveys in the study that have been started for this respondentId
     * @param $studyId
     * @param $respondentId
     * @return \Symfony\Component\HttpFoundation\Response - Has surveys property with an array of surveys
     */
    public function getRespondentStudySurveys ($studyId, $respondentId) {
        $validator = Validator::make([
            'respondentId' => $respondentId,
            'studyId' => $studyId
        ], [
            'respondentId' => "required|string|min:32|exists:respondent,id",
            'studyId' => "required|string|min:32|exists:study,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Invalid respondentId or studyId',
                'err' => $validator->errors()
            ], $validator->stausCode());
        }

        $surveys = Survey::whereNull('deleted_at')
            ->where('study_id', '=', $studyId)
            ->where('respondent_id', '=', $respondentId);

        return response()->json([
            'surveys' => $surveys
        ], Response::HTTP_OK);
    }

    public function importRespondents (Request $request, $studyId, RespondentService $respondentService) {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId
        ]), [
            'studyId' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $hasRespondentFile = $request->hasFile('file');
        if ($hasRespondentFile) {
            $respondentFile = $request->file('file');
            $importedRespondentIds = $respondentService->importRespondentsFromFile($respondentFile->getRealPath(), $studyId, $request->get('skip_header') === 'true');

            return response()->json([
              'respondents' => Respondent::with('photos', 'respondentConditionTags', 'names')->whereIn('id', $importedRespondentIds)->get()
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Provide a CSV file of respondent IDs and names'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function preloadRespondentData(Request $request, PreloadActionService $preloadActionService)
    {
        $hasFile = $request->hasFile('respondentPreloadDataCsv');

        if (! $hasFile) {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Provide a CSV file of respondent IDs and preload data.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();
            $file = $request->file('respondentPreloadDataCsv');
            $fileStream = fopen($file->getRealPath(), 'r+');
            $csv = Reader::createFromStream($fileStream);
            foreach ($csv->fetchAssoc() as $record) {
                switch($record['action_type']) {
                    case 'add-roster-row':
                        $preloadActionService::preloadAddRosterRow($record['respondent_id'], $record['question_id'], $record['payload']);
                        break;
                    default:
                        throw new Exception('Action types other than add-roster-row not implemented.');
                }
            }
            DB::commit();
        } catch (PDOException $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json(
                [
                    'msg' => 'Unable to process the .csv provided.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return response()->json(
            [],
            Response::HTTP_OK
        );
    }

    public function importRespondentPhotos(Request $request, $studyId, RespondentService $respondentService)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId
        ]), [
            'studyId' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $hasRespondentPhotoFile = $request->hasFile('file');
        if ($hasRespondentPhotoFile) {
          try {
            $respondentPhotoFile = $request->file('file');
            $nRespondentPhotos = $respondentService->importRespondentPhotos($respondentPhotoFile->getRealPath(), $studyId);
            return response()->json([
              'importedRespondentPhotos' => $nRespondentPhotos
            ], Response::HTTP_OK);
          } catch (Throwable $e) {
            return response()->json([
              'msg' => 'Request failed',
              'err' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
          }
        } else {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Provide a CSV file of respondent IDs and names'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllRespondents(Request $request)
    {
        // Default to limit = 100 and offset = 0
        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);

        $count = Respondent::count();
        $respondents = Respondent::with('photos', 'respondentConditionTags')
            ->whereNull('associated_respondent_id')
            ->limit($limit)
            ->offset($offset)
            ->get();


        return response()->json(
            ['respondents' => $respondents,
                'count' => $count,
                'limit' => $limit,
                'offset' => $offset],
            Response::HTTP_OK
        );
    }

    /**
     * Get all of the geoIds for the children of the provided geo ids
     * @param array $geos - An array of top level geo ids
     * @return array
     */
    private static function getChildGeos ($geos) {
        $parentGeos = array_replace([], $geos);
        $moreChildren = true;
        $c = 0;
        while ($moreChildren && $c < 10) {
            $c++;
            $moreChildren = false;
            $children = Geo::select('geo.id', 'geo_type.can_contain_respondent')->join('geo_type', 'geo.geo_type_id', '=', 'geo_type.id')->whereIn('geo.parent_id', $parentGeos)->get();
            if (count($children) > 0) {
                $moreChildren = true;
                $geos = array_replace($geos, $children->filter(function ($c) {return $c->can_contain_respondent;})->reduce(function ($arr, $c) {
                    array_push($arr, $c->id);
                    return $arr;
                }, []));
                $parentGeos = $children->reduce(function ($arr, $c) {
                    array_push($arr, $c->id);
                    return $arr;
                }, []);
            }
        }
        return $geos;
    }

    /**
     * Search all respondents in a study. Several query parameters are also accepted.
     * @param Request $request
     * @param $studyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchRespondentsByStudyId (Request $request, $studyId) {
        $query = $request->query('q');
        $conditionTags = $request->query('c');
        $orConditionTags = $request->query('oc');
        $geos = $request->query('g');
        $includeChildren = $request->query('i');
        $randomize = $request->query('r');
        $validator = Validator::make([
            'studyId' => $studyId,
            'associatedRespondent' => $request->get('associated_respondent_id'),
            'page' => $request->get('page'),
            'size' => $request->get('size'),
            'seed' => $request->get('seed')
        ], [
            'studyId' => 'required|string|min:36|exists:study,id',
            'associatedRespondent' => 'nullable|string|min:36|exists:respondent,id',
            'page' => 'nullable|integer|min:0',
            'size' => 'nullable|integer|min:0|max:200',
            'seed' => 'nullable|integer|min:0'
        ]);

        // Default to limit = 50 and offset = 0
        $page = $request->get('page', 0);
        $size = $request->get('size', $request->get('limit', 50));
        $seed = $request->get('seed', rand());
        $associatedRespondentId = $request->get('associated_respondent_id');

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $respondentQuery = Respondent::whereRaw('`respondent`.`id` in (select respondent_id from study_respondent where study_id = ?)', [$studyId])
            ->with('photos', 'respondentConditionTags', 'names');

        // Add name search
        if ($query) {
            $nameQuery = RespondentName::select('respondent_id')->distinct();
            $terms = explode(' ', $query);
            foreach ($terms as $term) {
                // TODO: prefer matches to first name first
                $nameQuery = $nameQuery->whereRaw("concat(' ', name) like ?", ["% $term%"]);
            }
            $respondentQuery = $respondentQuery->whereIn('id', $nameQuery);
        }

        // Add condition tag filter
        if ($conditionTags) {
            $tagNames = explode(',', $conditionTags);
            $respondentQuery = $respondentQuery
                ->whereHas('respondentConditionTags', function ($query) use ($tagNames) {
                    $query->whereIn('condition_tag.name', $tagNames);
                }, '=', count($tagNames));
        }

        // Add geo id filter
        $respondentQuery->where(function ($q) use ($geos, $includeChildren, $associatedRespondentId, $orConditionTags) {
            $q->where(function ($gq) use ($geos, $includeChildren) {
                $gq->whereNull('associated_respondent_id');
                if ($geos) {
                    $geoIds = explode(',', $geos);
                    if ($includeChildren) {
                        $geoIds = self::getChildGeos($geoIds);
                    }
                    $gq->whereHas('geos', function ($q) use ($geoIds) {
                        $q->whereIn('respondent_geo.geo_id', $geoIds);
                    });
                }
            });
            if (isset($associatedRespondentId)) {
                $q->orWhere('associated_respondent_id', '=', $associatedRespondentId);
            }

            // Or condition tag filters
            if (isset($orConditionTags)) {
                $q->orWhereRaw('`respondent`.`id` in (select distinct respondent_id from respondent_condition_tag where deleted_at is null and condition_tag_id in (select id from condition_tag where name in (?)))', [$orConditionTags]);
            }
        });

        if ($randomize) {
            $respondentQuery = $respondentQuery->inRandomOrder($seed);
        }

        $skip = $page * $size;
        $respondents = $respondentQuery->take($size)->skip($skip)->get();

        return response()->json(
            [
                'data' => $respondents,
                'seed' => $seed,
                'total' => 0,
                'page' => $page,
                'size' => $size
            ],Response::HTTP_OK
        );
    }

    public function searchRespondentsByStudyIdOld(Request $request, $studyId)
    {
        $validator = Validator::make([
            'studyId' => $studyId,
            'associatedRespondent' => $request->get('associated_respondent_id'),
            'limit' => $request->get('limit'),
            'offset' => $request->get('offset')
        ], [
            'studyId' => 'required|string|min:36|exists:study,id',
            'associatedRespondent' => 'nullable|string|min:36|exists:respondent,id',
            'limit' => 'nullable|integer|max:200|min:0',
            'offset' => 'nullable|integer|min:0'
        ]);

        // Default to limit = 50 and offset = 0
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);
        $associatedRespondentId = $request->get('associated_respondent_id');

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $q = $request->query('q');
        $c = $request->query('c');

        DB::enableQueryLog();
        if ($q) {
            $searchTerms = explode(" ", $q);
            $r1 = Respondent::with('photos', 'respondentConditionTags')
                ->selectRaw("*, 1 as score")
                ->whereRaw("id in (select respondent_id from study_respondent where study_id = ?)", [$studyId])
                ->whereRaw('`respondent`.`associated_respondent_id` is null or `respondent`.`associated_respondent_id` = ?', [$associatedRespondentId]);

            $r2 = Respondent::with('photos', 'respondentConditionTags')
                ->selectRaw("*, 2 as score")
                ->whereRaw("id in (select respondent_id from study_respondent where study_id = ?)", [$studyId])
                ->whereRaw('(associated_respondent_id is null or associated_respondent_id = ?)', [$associatedRespondentId]);

            if ($c) {
                $cArray = explode(",", $c);
                if (count($cArray) > 0) {
                    $r1 = $r1
                        ->whereHas('respondentConditionTags', function ($query) use ($cArray) {
                            $query
                                ->whereIn('condition_tag.name', $cArray);
                        }, '=', count($cArray));
                    $r2 = $r2
                        ->whereHas('respondentConditionTags', function ($query) use ($cArray) {
                            $query
                                ->whereIn('condition_tag.name', $cArray);
                        }, '=', count($cArray));
                    //$currentQuery = $respondents->toSql();
                    //Log::info('$currentQuery: ' . $currentQuery);
                }
            }

            for ($i = 0; $i < count($searchTerms); $i++) {
                $searchTerm = $searchTerms[$i];
                if ($i == 0) {
                    $searchString = $searchTerm . '%';
                    $r1 = $r1->whereRaw("name like ?", [$searchString]);
                    $searchString = '% ' . $searchTerm . '%';
                    $r2 = $r2->whereRaw("name like ?", [$searchString]);
                } else {
                    $searchString = '% ' . $searchTerm . '%';
                    $r1 = $r1->whereRaw("concat(' ', name) like ?", [$searchString]);
                    $r2 = $r2->whereRaw("concat(' ', name) like ?", [$searchString]);
                }
            }

            $respondents = $r1->union($r2)->orderBy('score', 'asc');

        } else {
            $respondents = Respondent::with('photos', 'respondentConditionTags', 'names')
                ->selectRaw("*, 1 as score")
                ->whereNull('associated_respondent_id')
                ->whereRaw("id in (select respondent_id from study_respondent where study_id = ?)", [$studyId]);

            if ($c) {
                $cArray = explode(",", $c);
                if (count($cArray) > 0) {
                    $respondents = $respondents
                        ->whereHas('respondentConditionTags', function ($query) use ($cArray) {
                            $query
                                ->whereIn('condition_tag.name', $cArray);
                        }, '=', count($cArray));
                    //$currentQuery = $respondents->toSql();
                    //Log::info('$currentQuery: ' . $currentQuery);
                }
            }
            $respondents = $respondents->distinct();
        }
//        $currentQuery = $respondents->toSql();
        $respondents = $respondents->limit($limit)->offset($offset)->get();
        $count = count($respondents);
        $currentQuery = DB::getQueryLog();
        Log::info(json_encode($currentQuery));
        DB::disableQueryLog();
        return response()->json(
            ['respondents' => $respondents,
                'count' => $count,
                'limit' => $limit,
                'offset' => $offset],
            Response::HTTP_OK
        );
    }

    public function getAllRespondentsByStudyId(Request $request, $studyId)
    {
        $validator = Validator::make([
            'studyId' => $studyId,
            'limit' => $request->get('limit'),
            'offset' => $request->get('offset')
        ], [
            'studyId' => 'required|string|min:36|exists:study,id',
            'limit' => 'nullable|integer|max:200|min:0',
            'offset' => 'nullable|integer|min:0'
        ]);

        // Default to limit = 100 and offset = 0
        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        //$studyModel = Study::with('respondents.photos')->where('id', $studyId)->get();
        $count = Respondent::whereHas('studies', function ($query) use ($studyId) {
            $query->where('study.id', '=', $studyId);
        })->count();

        $respondents = Respondent::with('photos', 'respondentConditionTags', 'names')
            ->whereHas('studies', function ($query) use ($studyId) {
                $query->where('study.id', '=', $studyId);
            })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'respondents' => $respondents,
            'count' => $count,
            'limit' => $limit,
            'offset' => $offset
        ],Response::HTTP_OK);
    }

    public function getRespondentCountByStudyId(Request $request, $studyId)
    {
        $validator = Validator::make(
            ['studyId' => $studyId],
            ['studyId' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $count = Respondent::whereHas('studies', function ($query) use ($studyId) {
            $query->where('study.id', '=', $studyId);
        })->count();

        return response()->json(
            ['count' => $count],
            Response::HTTP_OK
        );
    }

    public function addPhoto(Request $request, $respondent_id, RespondentPhotoService $respondentPhotoService)
    {
        $validator = Validator::make([
            'respondentId' => $respondent_id], [
            'respondentId' => 'required|string|min:36|exists:respondent,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $adapter = new Local(storage_path() . '/respondent-photos');
        $filesystem = new Filesystem($adapter);

        $respondent = Respondent::find($respondent_id);
        $hasFile = $request->hasFile('file');
        if ($hasFile and $respondent->exists()) {
            $file = $request->file('file');
            $stream = fopen($file->getRealPath(), 'r+');
            $extension = $file->getClientOriginalExtension();
            $newName = Uuid::uuid4();
            $fileName = $newName.'.'.$extension;
            $filesystem->writeStream($fileName, $stream);
            fclose($stream);

            $photo = $respondentPhotoService->createRespondentPhoto($fileName, $respondent_id);

            /*
            $photo = new Photo;
            $photoId = Uuid::uuid4();
            $photo->id = $photoId;
            $photo->file_name = $fileName;
            $photo->save();

            $respondentPhoto = new RespondentPhoto;
            $respondentPhoto->id = Uuid::uuid4();
            $respondentPhoto->respondent_id = $respondentId;
            $respondentPhoto->photo_id = $photoId;
            $maxCount = RespondentPhoto::where('respondent_id', $respondentId)->max('sort_order');
            $maxCount = ($maxCount == null) ? 1 : $maxCount + 1;
            $respondentPhoto->sort_order = $maxCount;
            $respondentPhoto->save();
            */

            return response()->json(
                ['photo' => $photo],
                Response::HTTP_OK
            );
        } else {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Invalid photo or invalid respondent id'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function create()
    {
        return view('respondents.create');
    }

    public function updateRespondent(Request $request, $id)
    {
        $validator = Validator::make([
            'respondentId' => $id,
            'name' => $request->get('name')
        ], [
            'respondentId' => 'required|string|min:36|exists:respondent,id',
            'name' => 'required|string|min:1|max:65535'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $respondent = Respondent::find($id);
        $respondent->name = $request->input('name');
        $respondent->save();

        return response()->json([
            'respondent' => $respondent
        ], Response::HTTP_OK);
    }

    public function createStudyRespondent (Request $request, RespondentService $respondentService, $studyId) {
        $validator = Validator::make([
            'name' => $request->get('name'),
            'geoId' => $request->get('geo_id'),
            'associatedRespondentId' => $request->get('associated_respondent_id'),
            'studyId' => $studyId
        ], [
            'name' => 'required|string|min:1|max:65535',
            'associatedRespondentId' => 'nullable|string|min:36|exists:respondent,id',
            'geoId' => 'nullable|string|min:36|exists:geo,id',
            'studyId' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $newRespondentModel = $respondentService->createRespondent(
            $request->input('name'),
            $studyId,
            null,
            $request->get('geo_id'),
            $request->get('associated_respondent_id')
        );



        $newRespondentModel->geos = $newRespondentModel->geos;
        $newRespondentModel->photos = $newRespondentModel->photos;
        $newRespondentModel->names = $newRespondentModel->names;

        return response()->json([
            'respondent' => $newRespondentModel
        ], Response::HTTP_OK);
    }

    public function createRespondent (Request $request, RespondentService $respondentService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:65535',
            'associated_respondent_id' => 'nullable|string|min:36|exists:respondent,id',
            'geo_id' => 'nullable|string|min:36|exists:geo,id',
            'study_id' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $newRespondentModel = $respondentService->createRespondent(
            $request->input('name'),
            $request->input('study_id'),
            $request->get('geo_id'),
            $request->get('associated_respondent_id')
        );

        return response()->json([
            'respondent' => $newRespondentModel
        ], Response::HTTP_OK);
    }

    public function removeRespondent($id)
    {
        $id = urldecode($id);

        $validator = Validator::make(
            ['respondentId' => $id],
            ['respondentId' => 'required|string|min:36|exists:respondent,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $respondentModel = Respondent::find($id);

        if ($respondentModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $respondentModel->delete();

        return response()->json([

        ]);
    }

    public function removeRespondentPhoto($respondentId, $photoId)
    {
        $validator = Validator::make([
            'respondentId' => $respondentId,
            'photoId' => $photoId
        ], [
            'respondentId' => 'required|string|min:36|exists:respondent_photo,respondent_id',
            'photoId' => 'required|string|min:36|exists:respondent_photo,photo_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $nDeleted = RespondentPhoto::where('photo_id', $photoId)
            ->where('respondent_id', $respondentId)
            ->delete();

        return response()->json([
            'rowsDeleted' => $nDeleted
        ]);
    }

    public function store()
    {
        $GUID = new DateTime();
        $respondent = new Respondent;
        $respondent->id = $GUID;
        $respondent->name = Input::get('name');
        $respondent->add_date = time();
        $respondent->modify_date = time();
        $respondent->save();

        return Redirect::to('/respondents')->with(array('title' => 'Trellis'));
    }

    public function getRespondentById($respondentId)
    {
        $respondentId = urldecode($respondentId);
        $validator = Validator::make([
            'respondentId' => $respondentId
        ], [
            'respondentId' => 'required|string|min:32|exists:respondent,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => "Invalid respondent id",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $respondent = Respondent::with('respondentConditionTags', 'photos', 'names', 'rGeos')->find($respondentId);
        $respondent->geos = $respondent->rGeos;
        unset($respondent->rGeos);
        return response()->json([
            'respondent' => $respondent
        ], Response::HTTP_OK);
    }

    public function getRespondentPhotos($respondentId)
    {
        $respondentId = urldecode($respondentId);
        $validator = Validator::make([
            'respondentId' => $respondentId
        ], [
            'respondentId' => 'required|string|min:32|exists:respondent,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => "Invalid respondent id",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $photos = RespondentPhoto::with('photo')->where('respondent_id', $respondentId)->get();

        return response()->json([
            'photos' => $photos
        ], Response::HTTP_OK);
    }

    public function deleteRespondentPhoto($respondentPhotoId)
    {
        $validator = Validator::make([
            'respondentPhotoId' => $respondentPhotoId
        ], [
            'respondentPhotoId' => 'required|string|min:36|exists:respondent_photo,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        RespondentPhoto::where('id', $respondentPhotoId)->delete();

        return response()->json([], Response::HTTP_OK);
    }

    public function updateRespondentPhotos(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'photos' => 'required|array'
            ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $photos = $request->input('photos');

        foreach ($photos as $photo) {
            $respondentPhoto = RespondentPhoto::find($photo['pivot']['id']);

            if ($respondentPhoto !== null) {
                // Update sort order and notes
                $respondentPhoto->sort_order = $photo['pivot']['sortOrder'];
                $respondentPhoto->notes = $photo['pivot']['notes'];
                $respondentPhoto->save();
            }
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Get an array of respondent fills for the specified respondent
     * @param {String} $respondentId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRespondentFillsById ($respondentId) {
        $respondentId = urldecode($respondentId);
        $validator = Validator::make([
            'respondent' => $respondentId
        ], [
            'respondent' => 'required|string|min:32|exists:respondent,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $fills = RespondentFill::where('respondent_id', $respondentId)->get();
        return response()->json([
            'fills' => $fills
        ], Response::HTTP_OK);
    }


    public function edit($id)
    {
        $respondent = Respondent::find($id);
        $input = Input::all();

        if (isset($input['flashCard'])) {
            $flashCard = $input['flashCard'];
        } else {
            $flashCard = '';
        }

//        $marital_status = DB::table('marital')
//            ->orderBy('marital_name', 'asc')
//            ->lists('marital_name','id');

        $respondent_geo_location = DB::table('geo')
            ->join('respondent', 'geo.id', '=', 'respondent.geo_id')
            ->where('respondent.id', '=', $respondent->id)
            ->select('geo.id', 'geo.name', 'respondent.name')
            ->get();

        if (count($respondent_geo_location) > 0) {
            $respondent->respondent_geo_location = $respondent_geo_location[0]->id;
        }

        global $geo_selection;
        $geo_selection = array();
        $this->get_geo_tree(0, 0);
        $geoMaster = $geo_selection;

        $images = $imageslArr = DB::table('photo')
            ->join('respondent_photo', 'photo.id', '=', 'respondent_photo.photo_id')
            ->where('respondent_photo.respondent_id', $id)
            ->select('file_name', 'photo.id')
            ->get();

        $data = array(
            "respondent_geo_location" => $respondent_geo_location,
            "respondent" => $respondent,
//            "marital" => $marital_status,
            "geoMaster" => $geoMaster,
            "images" => $images
        );


        return view('respondents.edit')->with(array('data' => $data, 'title' => 'Edit Respondent', 'flashCard' => $flashCard ));
    }


    public function update(Request $request, $id)
    {
        $input = Input::all();
        $respondent = Respondent::find($id);
        $respondent->name = $input['name'];
        $respondent->date_of_birth = date('Y-m-d', strtotime($input['date_of_birth']));
        $respondent->gender = $input['gender'];
        $respondent->marital_master_id = $input['marital_master_id'];
        $respondent->remarks = $input['remarks'];
        $respondent->modify_date = time();
        $respondent->save();

        $new_group = DB::table('group_master')->where('geo_element_id', $input['geo_master'])->first();

        DB::table('group_respondent')
            ->where('respondent_master_id', $respondent->id)
            ->update(array('group_master_id'=>$new_group->id));

        $flashCard =['type' => 'success', 'icon' => 'fa-check', 'message' => 'This respondent was successfully modified!'];

        return Redirect::action('respondents.edit', array('id'=> $id, 'title' => 'Edit Respondent', 'flashCard' => $flashCard));
    }

    public function destroy($id)
    {
        $respondent = Respondent::find($id);
        $respondent->status = 0;
        $respondent->save();

//        This code delete all images associated to the respondent
//
//        $respondent->delete();
//        $images = $imageslArr = DB::table('respondent_photo')->where('respondent_master_id', $id )->get();
//        foreach ($images as $image) {
//
//            App::make('UploadController')->destroy(array('id'=>$image->id, 'redirect' => 'no'));
//
//        }

        return Redirect::to('/respondents');
    }

    public function get_geo_tree($parent, $level)
    {
        global $geo_selection;

        $allGeo = DB::table('geo')
            ->where('parent_id', $parent)
            ->get();

        foreach ($allGeo as $row) {
            $row->element_name = str_repeat(' - ', $level)." ".$row->element_name;
            $geo_selection = array_merge_recursive($geo_selection, array($row->id => $row->element_name));
            self::get_geo_tree($row->id, $level+1);
        }
    }

}

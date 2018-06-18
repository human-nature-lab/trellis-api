<?php

namespace App\Http\Controllers;

use App\Models\Respondent;
use App\Models\Photo;
use App\Models\RespondentFill;
use App\Models\Study;
use App\Models\RespondentPhoto;
use App\Models\StudyRespondent;
use App\Services\RespondentPhotoService;
use App\Services\RespondentService;
use \DateTime;
use \Input;
use \DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Ramsey\Uuid\Uuid;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Log;
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

    public function importRespondents(Request $request, $studyId, RespondentService $respondentService)
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

        $hasRespondentFile = $request->hasFile('respondentCsvFile');
        if ($hasRespondentFile) {
            $respondentFile = $request->file('respondentCsvFile');
            $respondentFileStream = fopen($respondentFile->getRealPath(), 'r+');
            $respondentCsv = Reader::createFromStream($respondentFileStream);
            $nRespondents = 0;

            // Skip past header-row
            $skipHeader = $request->input('skipHeader');
            \Log::info('$skipHeader: ' . $skipHeader);

            if ($skipHeader === "true") {
                $respondentCsv->setOffset(1);
            }

            $respondentCsv->each(function ($row) use ($nRespondents, $studyId, $respondentService) {
                // TODO: incrementing $nRespondents here doesn't work
                // $nRespondents += 1;
                $respondentAssignedId = trim($row[0]);
                $respondentName = trim($row[1]);
                \Log::info('$respondentAssignedId: ' . $respondentAssignedId);
                \Log::info('$respondentName: ' . $respondentName);

                $respondentService->createRespondent($respondentName, $studyId, $respondentAssignedId);

                return true;
            });

            return response()->json(
                [ 'importedRespondents' => $nRespondents ],
                Response::HTTP_OK
            );
        } else {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Provide a CSV file of respondent IDs and names'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function importRespondentPhotos(Request $request, $studyId, RespondentPhotoService $respondentPhotoService)
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

        $nRespondentPhotos = 0;
        $hasRespondentPhotoFile = $request->hasFile('respondentPhotoZipFile');
        if ($hasRespondentPhotoFile) {
            $respondentPhotoFile = $request->file('respondentPhotoZipFile');
            $respondentPhotoZip = new ZipArchive;
            if ($respondentPhotoZip->open($respondentPhotoFile->getRealPath()) === TRUE) {
                for ($i = 0; $i < $respondentPhotoZip->numFiles; $i++) {
                    $fileName = $respondentPhotoZip->getNameIndex($i);
                    $fileInfo = pathinfo($fileName);
                    // TODO: Consider supporting other image types here
                    if ($fileInfo['extension'] === 'jpg') {
                        // TODO
                        Log::info('JPG: ' . $fileInfo['basename']);
                    }
                }
                $respondentPhotoZip->close();
                return response()->json(
                    [ 'importedRespondentPhotos' => $nRespondentPhotos ],
                    Response::HTTP_OK
                );
            } else {
                return response()->json([
                    'msg' => 'Request failed',
                    'err' => 'Unable to open the provided archive.'
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

    public function searchRespondentsByStudyId(Request $request, $study_id)
    {
        $validator = Validator::make(
            ['study_id' => $study_id],
            ['study_id' => 'required|string|min:36|exists:study,id']
        );

        // Default to limit = 50 and offset = 0
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $q = $request->query('q');
        $c = $request->query('c');

        if ($q) {
            $searchTerms = explode(" ", $q);
            $r1 = Respondent::with('photos', 'respondentConditionTags')
                ->selectRaw("*, 1 as score")
                ->whereRaw("id in (select respondent_id from study_respondent where study_id = ?)", [$study_id]);

            $r2 = Respondent::with('photos', 'respondentConditionTags')
                ->selectRaw("*, 2 as score")
                ->whereRaw("id in (select respondent_id from study_respondent where study_id = ?)", [$study_id]);

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

            //$currentQuery = $respondents->toSql();
            //Log::info('$currentQuery: ' . $currentQuery);


            $respondents = $respondents->limit($limit)->offset($offset)->get()->unique();
            $count = count($respondents);

            return response()->json(
                ['respondents' => $respondents,
                    'count' => $count,
                    'limit' => $limit,
                    'offset' => $offset],
                Response::HTTP_OK
            );
        } else {
            $respondents = Respondent::with('photos', 'respondentConditionTags')
                ->selectRaw("*, 1 as score")
                ->whereRaw("id in (select respondent_id from study_respondent where study_id = ?)", [$study_id]);

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

            $respondents = $respondents->limit($limit)->offset($offset)->get();
            $count = count($respondents);

            return response()->json(
                ['respondents' => $respondents,
                    'count' => $count,
                    'limit' => $limit,
                    'offset' => $offset],
                Response::HTTP_OK
            );


        }
    }

    public function getAllRespondentsByStudyId(Request $request, $study_id)
    {
        $validator = Validator::make(
            ['study_id' => $study_id],
            ['study_id' => 'required|string|min:36|exists:study,id']
        );

        // Default to limit = 100 and offset = 0
        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        //$studyModel = Study::with('respondents.photos')->where('id', $study_id)->get();
        $count = Respondent::whereHas('studies', function ($query) use ($study_id) {
            $query->where('study.id', '=', $study_id);
        })->count();

        $respondents = Respondent::with('photos', 'respondentConditionTags', 'names')->whereHas('studies', function ($query) use ($study_id) {
            $query->where('study.id', '=', $study_id);
        })
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

    public function getRespondentCountByStudyId(Request $request, $study_id)
    {
        $validator = Validator::make(
            ['study_id' => $study_id],
            ['study_id' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $count = Respondent::whereHas('studies', function ($query) use ($study_id) {
            $query->where('study.id', '=', $study_id);
        })->count();

        return response()->json(
            ['count' => $count],
            Response::HTTP_OK
        );
    }

    public function addPhoto(Request $request, $respondent_id, RespondentPhotoService $respondentPhotoService)
    {
        $validator = Validator::make([
            'respondent_id' => $respondent_id], [
            'respondent_id' => 'required|string|min:36'
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
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:1|max:65535|required'
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

    public function createRespondent(Request $request, RespondentService $respondentService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:1|max:65535',
            'study_id' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $newRespondentModel = $respondentService->createRespondent ($request->input('name'), $request->input('study_id'));

        return response()->json([
            'respondent' => $newRespondentModel
        ], Response::HTTP_OK);
    }

    public function removeRespondent($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36']
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
        $validator = Validator::make(
            ['respondent_id' => $respondentId,
                'photo_id' => $photoId],
            ['respondent_id' => 'required|string|min:36|exists:respondent_photo,respondent_id',
                'photo_id' => 'required|string|min:36|exists:respondent_photo,photo_id']
        );

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

        $respondent = Respondent::with('respondentConditionTags', 'photos')->find($respondentId);
        return response()->json([
            'respondent' => $respondent
        ], Response::HTTP_OK);
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

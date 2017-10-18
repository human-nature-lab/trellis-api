<?php

namespace App\Http\Controllers;

use App\Models\Respondent;
use App\Models\Photo;
use App\Models\Study;
use App\Models\RespondentPhoto;
use App\Models\StudyRespondent;
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

class RespondentController extends Controller
{
    public function index()
    {
        $respondents = Respondent::where('deleted_at', null)
            ->orderBy('created_at')
            ->paginate(20);

        return view('respondents.respondents', array('title' => 'Respondents', 'respondents' => $respondents));
    }

    public function getAllRespondents(Request $request)
    {
        // Default to limit = 100 and offset = 0
        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);

        $count = Respondent::count();
        $respondents = Respondent::with('photos', 'conditionTags')
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

        // Default to limit = 100 and offset = 0
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        //$studyModel = Study::with('respondents.photos')->where('id', $study_id)->get();
        $q = $request->query('q');
        $c = $request->query('c');

        $respondents = Respondent::with('photos', 'conditionTags')
            ->whereHas('studies', function ($query) use ($study_id) {
                $query
                    ->where('study.id', '=', $study_id);
            });
        if ($q) {
            $respondents = $respondents->where('name', 'LIKE', $q . '%');
        }

        if ($c) {
            $cArray = explode(",", $c);
            if (count($cArray) > 0) {
                $respondents = $respondents
                    ->whereHas('conditionTags', function ($query) use ($cArray) {
                        $query
                            ->whereIn('condition_tag.name', $cArray);
                    }, '=', count($cArray));
                $currentQuery = $respondents->toSql();
                Log::info('$currentQuery: ' . $currentQuery);
            }
        }

        $count = $respondents->count();

        $respondents = $respondents->limit($limit)->offset($offset)->get();

        /*
        $respondents = Respondent::with('photos', 'conditionTags')
            ->whereHas('studies', function ($query) use ($study_id) {
                $query->where('study.id', '=', $study_id);
            })
            ->whereHas('conditionTags', function ($query) use ($c) {
                $query
                    ->where('condition_tag.name', 'in', $c);

            })
            ->where('name', 'LIKE', $q . '%')
            ->limit($limit)
            ->offset($offset)
            ->get();
        */

        return response()->json(
            ['respondents' => $respondents,
                'count' => $count,
                'limit' => $limit,
                'offset' => $offset],
            Response::HTTP_OK
        );
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

        $respondents = Respondent::with('photos', 'conditionTags')->whereHas('studies', function ($query) use ($study_id) {
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

    public function addPhoto(Request $request, $respondentId)
    {
        $validator = Validator::make([
            'respondent_id' => $respondentId], [
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

        $respondent = Respondent::find($respondentId);
        $hasFile = $request->hasFile('file');
        if ($hasFile and $respondent->exists()) {
            $file = $request->file('file');
            $stream = fopen($file->getRealPath(), 'r+');
            $extension = $file->getClientOriginalExtension();
            $newName = Uuid::uuid4();
            $fileName = $newName.'.'.$extension;
            $filesystem->writeStream($fileName, $stream);
            fclose($stream);

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

    public function createRespondent(Request $request)
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

        $respondentId = Uuid::uuid4();
        $respondentName = $request->input('name');

        $newRespondentModel = new Respondent;
        $newRespondentModel->id = $respondentId;
        $newRespondentModel->name = $respondentName;
        $newRespondentModel->save();

        $studyId = $request->input('study_id');
        $studyRespondentId = Uuid::uuid4();

        $newStudyRespondentModel = new StudyRespondent;
        $newStudyRespondentModel->id = $studyRespondentId;
        $newStudyRespondentModel->respondent_id = $respondentId;
        $newStudyRespondentModel->study_id = $studyId;
        $newStudyRespondentModel->save();

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

    public function show($id)
    {
        $respondent = Respondent::find($id);
        return view('respondents.show')->with('respondent', $respondent);
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

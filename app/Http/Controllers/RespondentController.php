<?php

namespace App\Http\Controllers;

use App\Models\Respondent;
use \DateTime;
use \Input;
use \DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Ramsey\Uuid\Uuid;


class RespondentController extends Controller {

     public function index()
    {
        $respondents = Respondent::where('deleted_at', null)
            ->orderBy('created_at')
            ->paginate(20);

        return view('respondents.respondents', array('title' => 'Respondents', 'respondents' => $respondents));
    }

    public function getAllRespondents(Request $request) {

        $respondents = Respondent::with('photos')->get();

        return response()->json(
            ['respondents' => $respondents],
            Response::HTTP_OK
        );
    }

    public function create(){

        return view('respondents.create');

    }

    public function updateRespondent(Request $request, $id) {
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

    public function createRespondent(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'string|min:1|max:65535'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $id = Uuid::uuid4();
        $respondentName = $request->input('name');

        $newRespondentModel = new Respondent;
        $newRespondentModel->id = $id;
        $newRespondentModel->name = $respondentName;
        $newRespondentModel->save();

        return response()->json([
            'respondent' => $newRespondentModel
        ], Response::HTTP_OK);
    }

    public function removeRespondent($id) {

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

    public function store(){

        $GUID = new DateTime();
        $respondent = new Respondent;
        $respondent->id = $GUID;
        $respondent->name = Input::get('name');
        $respondent->add_date = time ();
        $respondent->modify_date = time ();
        $respondent->save();

        return Redirect::to('/respondents')->with(array('title' => 'Trellis'));

    }

    public function show($id){

        $respondent = Respondent::find($id);
        return view('respondents.show')->with('respondent', $respondent);

    }


    public function edit($id){

        $respondent = Respondent::find($id);
        $input = Input::all();

        if(isset($input['flashCard'])){
            $flashCard = $input['flashCard'];
        }else{
            $flashCard = '';
        }

//        $marital_status = DB::table('marital')
//            ->orderBy('marital_name', 'asc')
//            ->lists('marital_name','id');

        $respondent_geo_location = DB::table('geo')
            ->join('respondent', 'geo.id', '=', 'respondent.geo_id' )
            ->where('respondent.id', '=', $respondent->id)
            ->select('geo.id', 'geo.name', 'respondent.name')
            ->get();

        if (count($respondent_geo_location) > 0 ) {
            $respondent->respondent_geo_location = $respondent_geo_location[0]->id;
        }

        global $geo_selection;
        $geo_selection = array();
        $this->get_geo_tree(0,0);
        $geoMaster = $geo_selection;

        $images = $imageslArr = DB::table('photo')
            ->join('respondent_photo', 'photo.id', '=', 'respondent_photo.photo_id')
            ->where('respondent_photo.respondent_id', $id )
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


    public function update(Request $request, $id){

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

    public function destroy($id){

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

    function get_geo_tree($parent, $level)  {
        global $geo_selection;

        $allGeo = DB::table('geo')
            ->where('parent_id', $parent)
            ->get();

        foreach($allGeo as $row){
            $row->element_name = str_repeat(' - ',$level)." ".$row->element_name;
            $geo_selection = array_merge_recursive($geo_selection,array($row->id => $row->element_name));
            self::get_geo_tree($row->id, $level+1);
        }

    }

}

<?php namespace App\Http\Controllers;

use App\Models\Action;
use DB;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;

class ActionController extends Controller {

    /**
     * Validate and store an array of actions
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storeActions(Request $request) {

        // TODO: Validate these actions before inserting them blindly
        $actions = $request->input('actions');

        $actions = array_map(function($action){
            if(!isset($action['id'])) {
                $action['id'] = Uuid::uuid4();
            }
            return $action;
        }, $actions);

        DB::transaction(function() use ($actions) {
            Action::insert($actions);
        });

        // Return array of the added ids
        return response()->json([
            'msg' => "Added actions successfully",
            'actions' => array_map(function($action){
                return $action['id'];
            }, $actions)
        ], Response::HTTP_OK);

    }

    /**
     * Return an array of all the actions for that survey
     * @param $surveyId
     */
    public function getSurveyActions($surveyId){
        $validator = Validator::make([
            'survey_id' => $surveyId
        ], [
            'survey_id' => 'required|string|min:32|exists:survey,id'
        ]);
        if($validator->fails()){
            return response()->json([
                'msg' => "Invalid survey id",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }
        return response()->json([
            'actions' => Action::where('survey_id', '=', $surveyId)
        ], Response::HTTP_OK);
    }

	/**
	 * Remove the specified resource from storage.
	 * DELETE /action/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$validator = Validator::make([
		    'action_id' => $id
        ], [
            'action_id' => 'required|string|min:32|exists:action,id'
        ]);

		if($validator->fails()){
		    return response()->json([
		        'msg' => "Invalid action id",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $action = Action::find($id);
		$action->delete();
		return response()->json([
		    'msg' => 'Action has been deleted',
		    'id' => $id
        ], Response::HTTP_OK);

	}

}
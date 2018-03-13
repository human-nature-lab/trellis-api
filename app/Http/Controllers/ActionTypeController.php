<?php namespace App\Http\Controllers;

use App\Models\ActionType;
use Illuminate\Http\Response;
use Validator;

class ActionController extends Controller {

    /**
     * Get all of the action types in the database
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getActionTypes(){
        return response()->json([
            'action-types' => ActionType::all()
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
		    'action_type_id' => $id
        ], [
            'action_type_id' => 'required|string|min:32|exists:action_type,id'
        ]);

		if($validator->fails()){
		    return response()->json([
		        'msg' => "Invalid action type id",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $action = ActionType::find($id);
		$action->delete();
		return response()->json([
		    'msg' => 'Action type has been deleted',
		    'id' => $id
        ], Response::HTTP_OK);

	}

}
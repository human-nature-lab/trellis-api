<?php namespace App\Http\Controllers;



use App\Models\RespondentConditionTag;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class ConditionTagController extends Controller {

	/**
	 * Display a listing of the resource.
	 * GET /conditiontag
	 *
	 * @return Response
	 */
	public function index()
	{
        $conditionTags = DB::table('respondent_condition_tag')
            ->join('');
	}


	public function respondentConditionTags($respondentId){
	    $validator = Validator::make([
	        'respondentId' => $respondentId
        ], [
            'respondentId' => 'required|string|min:32|exists:respondent,id'
        ]);
	    if($validator->fails()){
	        return response()->json([
	            'err' => $validator->errors(),
                'msg' => "Invalid respondent id"
            ], $validator->statusCode());
        }

        $tags = RespondentConditionTag::where('respondent_id', '=', $respondentId)
            ->whereNull('respondent.deleted_at')
            ->innerJoin('condition_tag', 'respondent_condition_tag.condition_tag_id', '=', 'condition_tag.id')
            ->select('condition_tag.id, condition_tag.name');

	    return response()->json([
	        'tags' => $tags
        ], Response::HTTP_OK);

    }

	/**
	 * TODO: Store a respondent condition tag. Creates the condition tag if it doesn't already exist
	 * POST /conditiontag
	 *
	 * @return Response
	 */
	public function storeRespondentConditionTag(Request $request, $respondentId)
	{

	}

	/**
	 * TODO: Remove the condition tag from the database
	 * DELETE /conditiontag/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
<?php namespace App\Http\Controllers;



use App\Models\ConditionTag;
use App\Models\RespondentConditionTag;
use App\Services\ConditionTagService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Throwable;
use Validator;
use Illuminate\Support\Facades\Log;

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

    /**
     * Get an array of all of the condition tags in the database
     * @return \Illuminate\Http\JsonResponse
     */
	public function getAllConditionTags () {
	    return response()->json([
	        'condition_tags' => ConditionTag::all()
        ], Response::HTTP_OK);
    }

    /**
     * Get an array of distinct condition tag names
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConditionTagNames () {
        $conditionTagNames = DB::table('condition_tag')->select('name')->distinct()->pluck('name');
        return response()->json([
            'condition_tag_names' => $conditionTagNames
        ], Response::HTTP_OK);
    }

    /**
     * Create a condition tag with the given name
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createConditionTag (Request $request) {
	    $validator = Validator::make([
	        'name' => $request->get('name')
        ], [
            'name' => 'required|string|min:3'
        ]);

	    if ($validator->fails()) {
	        return rsponse()->json([
	            'msg' => $validator->errors()
            ], $validator->statusCode());
        }

	    $tag = ConditionTag::create([
	        'id' => Uuid::uuid4(),
            'name' => $request->get('name')
        ]);

	    return response()->json([
	        'condition_tag' => $tag
        ]);
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
     * Creates a Respondent condition tag using a condition tag that already exists
     * @param $respondentId
     * @param $conditionTagId
     * @return \Illuminate\Http\JsonResponse
     */
	public function createRespondentConditionTag ($respondentId, $conditionTagId) {
	    $respondentId = urldecode($respondentId);
	    $conditionTagId = urldecode($conditionTagId);
        $validator = Validator::make([
            'respondentId' => $respondentId,
            'conditionTagId' => $conditionTagId
        ], [
            'respondentId' => 'required|string|min:36|exists:respondent,id',
            'conditionTagId' => 'required|string|min:36|exists:condition_tag,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $tag = RespondentConditionTag::create([
            'id' => Uuid::uuid4(),
            'respondent_id' => $respondentId,
            'condition_tag_id' => $conditionTagId
        ]);

        return response()->json([
            'condition_tag' => $tag
        ], Response::HTTP_CREATED);

	}

    /**
     * Removes a respondent condition tag
     * @param string $respondentId
     * @param string $respondentConditionTagId
     * @return \Illuminate\Http\JsonResponse
     */
	public function deleteRespondentConditionTag ($respondentId, $respondentConditionTagId) {
		try {
		    RespondentConditionTag::destroy($respondentConditionTagId);
            return response()->json([
                'msg' => "Respondent condition tag, $respondentConditionTagId, has been removed"
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
		    return response()->json([
		        'msg' => "Unable to delete condition tag with id, $respondentConditionTagId"
            ], Response::HTTP_BAD_REQUEST);
        }

	}

  public function importRespondentConditionTags (Request $request, ConditionTagService $ctService, $studyId) {
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

    $hasFile = $request->hasFile('file');
    if ($hasFile) {
      $file = $request->file('file');
      try {
        DB::beginTransaction();
        $importedRespondentIds = $ctService->importRespondentConditionTagsFromFile($file->getRealPath(), $studyId);
        DB::commit();
      } catch (Throwable $e) {
        DB::rollBack();
        Log::error($e);
        return response()->json([
          'msg' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
      }
      return response()->json([
        'respondent_condition_tags' => count($importedRespondentIds)
      ], Response::HTTP_OK);
    } else {
      return response()->json([
        'msg' => 'Request failed',
        'err' => 'Provide a CSV file of respondent ids and condition tags'
      ], Response::HTTP_BAD_REQUEST);
    }
  }

}
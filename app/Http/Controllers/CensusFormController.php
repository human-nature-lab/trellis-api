<?php namespace App\Http\Controllers;

use App\Models\CensusType;
use App\Models\Form;
use App\Models\StudyForm;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class CensusFormController extends Controller {

    /**
     * Get the complete list of possible census form types
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCensusFormTypes () {
        $types = CensusType::all();
        return response()->json([
            'types' => $types
        ], Response::HTTP_OK);
    }

    public function getStudyCensusForm (Request $request, $studyId) {

        $validator = Validator::make([
            'studyId' => $studyId,
            'censusType' => $request->get('census_type')
        ], [
            'studyId' => 'required|string|min:36|exists:study,id',
            'censusType' => 'required|string|min:36|exists:census_type,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $studyForm = StudyForm::where('study_id', $studyId)
            ->where('census_type_id', $request->get('census_type'))
            ->with('form')
            ->first();

        if (!$studyForm) {
            return response()->json([
                'form' => null
            ], Response::HTTP_BAD_REQUEST);
        }

        $form = Form::find($studyForm->form_master_id);

        return response()->json([
            'form' => $form
        ], Response::HTTP_OK);
    }

}
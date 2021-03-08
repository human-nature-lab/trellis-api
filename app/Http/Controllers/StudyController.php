<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use App\Models\StudyParameter;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use App\Services\StudyService;
use App\Models\Study;
use App\Models\StudyLocale;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class StudyController extends Controller {

  public function getUsers (string $studyId) {
    Validator::make([
      'study' => $studyId,
    ], [
      'study' => 'required|string|exists:study,id'
    ])->validate();
    
    return User::whereIn('id', function ($q) use ($studyId) {
      return $q->
        select('user_id')->
        from('user_study')->
        where('study_id', $studyId)->
        whereNull('deleted_at');
    })->get();
  }

  public function getStudy(Request $request, $id) {
    $validator = Validator::make(
      ['studyId' => $id],
      ['studyId' => 'required|string|min:36']
    );

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    //$studyModel = Study::find($id);
    $studyModel = Study::with('locales')->find($id);

    if ($studyModel === null) {
      return response()->json([
        'msg' => 'URL resource not found'
      ], Response::HTTP_OK);
    }

    return response()->json([
      'study' => $studyModel
    ], Response::HTTP_OK);
  }

  public function getAllStudies(Request $request) {
    $user = $request->user();
    if ($user->role == "ADMIN") {
      return response()->json([
        'studies' => Study::whereNull('deleted_at')
          ->with('locales', 'defaultLocale', 'parameters')
          ->get()
      ], Response::HTTP_OK);
    } else {
      // Only return studies assigned to the logged in user
      $studies = $user->studies()->get();
      return response()->json(
        ['studies' => $studies],
        Response::HTTP_OK
      );
    }
  }

  public function getAllStudiesComplete() {
    return response()->json([
      'studies' => Study::whereNull('deleted_at')
        ->with('locales', 'defaultLocale', 'parameters')
        ->get()
    ], Response::HTTP_OK);
  }

  public function updateStudy(Request $request, $id) {
    $validator = Validator::make(array_merge($request->all(), [
      'studyId' => $id
    ]), [
      'studyId' => 'required|string|min:36|exists:study,id',
      'name' => 'nullable|string|min:1',
      'photo_quality' => 'required|integer|between:1,100',
      'default_locale_id' => 'required|string|min:36|exists:locale,id'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $studyModel = Study::with('defaultLocale', 'locales')->find($id);

    if ($studyModel === null) {
      return response()->json([
        'msg' => 'URL resource not found'
      ], Response::HTTP_NOT_FOUND);
    }

    $studyModel->fill($request->input());
    $studyModel->save();

    return response()->json([
      'study' => $studyModel
    ], Response::HTTP_OK);
  }

  public function removeStudy(Request $request, $id) {
    $validator = Validator::make(
      ['studyId' => $id],
      ['studyId' => 'required|string|min:36|exists:study,id']
    );

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $studyModel = Study::find($id);
    $studyModel->delete();

    return response()->json([]);
  }

  public function createStudy(Request $request, StudyService $studyService) {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|min:1',
      'photo_quality' => 'required|integer|between:1,100',
      'default_locale_id' => 'required|string|min:1'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $studyName = $request->input('name');
    $studyPhotoQuality = $request->input('photo_quality');
    $studyDefaultLocaleId = $request->input('default_locale_id');

    $newStudyModel = new Study;
    $studyId = Uuid::uuid4();
    $newStudyModel->id = $studyId;
    $newStudyModel->name = $studyName;
    $newStudyModel->photo_quality = $studyPhotoQuality;
    $newStudyModel->default_locale_id = $studyDefaultLocaleId;
    $newStudyModel->save();

    // Add the default locale ID to the study's locales
    $studyService::addLocale($studyId, $studyDefaultLocaleId);

    $returnStudy = Study::with('defaultLocale', 'locales')->find($studyId);

    return response()->json([
      'study' => $returnStudy
    ], Response::HTTP_OK);
  }

  public function saveLocale(StudyService $studyService, $studyId, $localeId) {
    $validator = Validator::make([
      'studyId' => $studyId,
      'localeId' => $localeId
    ], [
      'studyId' => 'required|string|min:36',
      'localeId' => 'required|string|min:36'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $studyService::addLocale($studyId, $localeId);

    /*
        $study = Study::findOrFail($studyId);
        $locale = Locale::findOrFail($localeId);
        $studyLocale = new StudyLocale;
        $studyLocale->id = Uuid::uuid4();
        $studyLocale->study_id = $studyId;
        $studyLocale->locale_id = $localeId;
        $studyLocale->save();
        //$study->locales()->save($locale);
        */
    $studyModel = Study::with('locales')->find($studyId);
    return response()->json(
      ['study' => $studyModel],
      Response::HTTP_OK
    );
  }

  public function deleteLocale($studyId, $localeId) {
    $validator = Validator::make([
      'studyId' => $studyId,
      'localeId' => $localeId
    ], [
      'studyId' => 'required|string|min:36|exists:study,id',
      'localeId' => 'required|string|min:36'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $studyLocale = StudyLocale::where('study_id', $studyId)
      ->where('locale_id', $localeId)
      ->firstOrFail();

    $studyLocale->delete();

    return response()->json(
      [],
      Response::HTTP_OK
    );
  }

  public function createOrUpdateParameter(Request $request, $studyId) {

    $validator = Validator::make(array_merge($request->all(), [
      'studyId' => $studyId
    ]), [
      'studyId' => 'required|string|min:36'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => "Validation failed",
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    if ($request->id === null) {

      $studyParameter = new StudyParameter();
      $studyParameter->id = Uuid::uuid4();
      $studyParameter->study_id = $studyId;
    } else {

      $studyParameter = StudyParameter::find($request->id);
    }

    // Check if the parameter exists
    $parameterModel = Parameter::where('name', $request->name)->first();

    if ($parameterModel === null) {
      return response()->json([
        'msg' => "Parameter name is invalid or does not exist"
      ], Response::HTTP_NOT_FOUND);
    }

    // Save the parameter
    $studyParameter->parameter_id = $parameterModel->id;
    $studyParameter->val = $request->val;
    $studyParameter->save();

    return response()->json([
      'parameter' => $studyParameter
    ], Response::HTTP_OK);
  }

  public function deleteParameter(Request $request, $studyId, $parameterId) {

    StudyParameter::destroy($parameterId);

    return response()->json([
      'msg' => "$parameterId deleted successfully"
    ], Response::HTTP_OK);
  }

  public function getParameterTypes() {

    // See QuestionParamController::getParameterTypes

  }

  public function getLocales($studyId) {

    $validator = Validator::make([
      'studyId' => $studyId
    ], [
      'studyId' => 'required|string|min:32|exists:study,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => 'Invalid study id',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $locales = StudyLocale::where('study_locale.study_id', '=', $studyId)
      ->join('locale', 'study_locale.locale_id', '=', 'locale.id')->get();
    return response()->json([
      'locales' => $locales
    ], Response::HTTP_OK);
  }
}

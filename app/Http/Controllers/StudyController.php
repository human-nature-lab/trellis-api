<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Study;
use App\Models\Locale;
use App\Models\StudyLocale;

class StudyController extends Controller
{

	public function getStudy(Request $request, $id) {

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

//		$studyModel = Study::select('study.id', 'study.name', 'study.photo_quality', 'l.language_name', 'study.default_locale_id')
//			->join('locale AS l', 'l.id', '=', 'default_locale_id')
//			->get();
        $studyModel = Study::with('locales', 'defaultLocale')->get();

		return response()->json(
			['studies' => $studyModel],
			Response::HTTP_OK
		);
	}

	public function updateStudy(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $id
		]), [
			'id' => 'required|string|min:36|exists:study,id',
			'name' => 'string|min:1',
			'photo_quality' => 'required|integer|between:1,100',
			'default_locale_id' => 'required|string|min:36|exists:locale,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyModel = Study::find($id);

		if ($studyModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$studyModel->fill($request->input());
		$studyModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function removeStudy(Request $request, $id) {

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

		$studyModel = Study::find($id);

		if ($studyModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}

		$studyModel->delete();

		return response()->json([

		]);
	}

	public function createStudy(Request $request) {

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

		$returnStudy = Study::with('defaultLocale', 'locales')->find($studyId);

		return response()->json([
			'study' => $returnStudy
		], Response::HTTP_OK);
	}

    public function saveLocale($studyId, $localeId) {
        $validator = Validator::make([
            'study_id' => $studyId,
            'locale_id' => $localeId], [
            'study_id' => 'required|string|min:36',
            'locale_id' => 'required|string|min:36'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $study = Study::findOrFail($studyId);
        $locale = Locale::findOrFail($localeId);
        $studyLocale = new StudyLocale;
        $studyLocale->id = Uuid::uuid4();
        $studyLocale->study_id = $studyId;
        $studyLocale->locale_id = $localeId;
        $studyLocale->save();
        //$study->locales()->save($locale);
        $studyModel = $study::with('locales')->get();
        return response()->json(
            ['study' => $studyModel],
            Response::HTTP_OK
        );
    }

    public function deleteLocale($studyId, $localeId) {
        $validator = Validator::make([
            'study_id' => $studyId,
            'locale_id' => $localeId], [
            'study_id' => 'required|string|min:36',
            'locale_id' => 'required|string|min:36'
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

}

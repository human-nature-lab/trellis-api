<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Translation;
use App\Models\TranslationText;

class TranslationController extends Controller
{
    public function removeTranslation(Request $request, $id)
    {
        $validator = Validator::make(
            ['translationId' => $id],
            ['translationId' => 'required|string|min:36|exists:translation,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $translationModel = Translation::find($id);
        $translationTextModel = TranslationText::where('translation_id', '=', $id)->get();

        if ($translationTextModel !== null) {
            return response()->json([
                'msg' => 'Translation parent still in use',
            ], Response::HTTP_CONFLICT);
        }

        $translationModel->delete();

        return response()->json([

        ], Response::HTTP_NO_CONTENT);
    }

    public function createTranslation(Request $request) {
        $newTranslationModel = new Translation;
        $newTranslationModel->id = Uuid::uuid4();
        $newTranslationModel->save();

        return response()->json([
            'translation' => $newTranslationModel
        ], Response::HTTP_OK);
    }

    public function getTranslation (String $translationId) {
        $translation = Translation::with('translationText')->find($translationId);
        return response()->json([
            'translation' => $translation
        ], Response::HTTP_OK);
    }

    public function getTranslations(Request $request) {
      $ids = $request->query('id', []);
      $translations = Translation::with('translationText')->whereIn('id', $ids)->get();
      return response()->json([
          'translations' => $translations
      ], Response::HTTP_OK);
    }

    public function getTranslationText($translationId) {
        $translationText = Translation::find($translationId)->translationText()->get();
        return response()->json([
            'translation_text' => $translationText
        ], Response::HTTP_OK);
    }
}

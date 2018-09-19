<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\TranslationText;
use Log;

class TranslationTextController extends Controller
{
    public function getTranslationText(Request $request, $id)
    {
        $validator = Validator::make(
            ['translationTextId' => $id],
            ['translationTextId' => 'required|string|min:36|exists:translation_text,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
               'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $translationTextModel = TranslationText::find($id);

        return response()->json([
            'translationText' => $translationTextModel
        ], Response::HTTP_OK);
    }

    public function getAllTranslationTexts(Request $request)
    {
        $translationTextModel = Form::get();

        return response()->json(
            ['translationTexts' => $translationTextModel],
            Response::HTTP_OK
        );
    }

    public function updateTranslatedTextById(Request $request, $translationTextId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'translationTextId' => $translationTextId
        ]), [
            'translated_text' => 'required|string',
            'translationTextId' => 'required|string|min:36|exists:translation_text,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $translationTextModel = TranslationText::find($translationTextId);
        $translationTextModel->translated_text = $request->input('translated_text');
        $translationTextModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function updateTranslationText(Request $request, $translationId, $textId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'translationId' => $translationId,
            'translationTextId' => $textId
        ]), [
            'translated_text' => 'required|string',
            'translationId' => 'nullable|string',
            'translationTextId' => 'required|string|min:36|exists:translation_text,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $translationTextModel = TranslationText::find($textId);
        $translationTextModel->translated_text = $request->input('translated_text');
        $translationTextModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function removeTranslationText(Request $request, $id)
    {
        $validator = Validator::make(
            ['translationTextId' => $id],
            ['translationTextId' => 'required|string|min:36|exists:translation_text,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $translationTextModel = TranslationText::find($id);
        $translationTextModel->delete();

        return response()->json([

        ], Response::HTTP_NO_CONTENT);
    }

    public function createTranslationText(Request $request, $translationId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'translationId' => $translationId
        ]), [
            'translationId' => 'required|string|min:36|exists:translation,id',
            'locale_id' => 'required|string|min:36|exists:locale,id',
            'translated_text' => 'required|string'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $translationTextId = Uuid::uuid4();
        $localeId = $request->input('locale_id');
        $translatedText = $request->input('translated_text');

        $newTranslationTextModel = new TranslationText;
        $newTranslationTextModel->id = $translationTextId;
        $newTranslationTextModel->translation_id = $translationId;
        $newTranslationTextModel->locale_id = $localeId;
        $newTranslationTextModel->translated_text = $translatedText;
        $newTranslationTextModel->save();

        $returnTranslationTextModel = TranslationText::with('locale')->find($translationTextId);

        return response()->json([
            'translationText' => $returnTranslationTextModel
        ], Response::HTTP_OK);
    }
}

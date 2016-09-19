<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Locale;


class LocaleController extends Controller
{
    public function getLocale(Request $request, $id) {

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

        $localeModel = Locale::find($id);

        if ($localeModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'locale' => $localeModel
        ], Response::HTTP_OK);
    }

    public function getAllLocales(Request $request) {

        $localeModel = Locale::get();

        return response()->json(
            ['locales' => $localeModel],
            Response::HTTP_OK
        );
    }

    public function updateLocale(Request $request, $id) {

        $validator = Validator::make(array_merge($request->all(),[
            'id' => $id
        ]), [
            'id' => 'required|string|min:36',
            'language_name' => 'string|min:1|max:255',
            'language_native' => 'string|min:1|max:255',
            'language_tag' => 'string|min:2|max:3'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $localeModel = Locale::find($id);

        if ($localeModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $localeModel->fill($request->input());
        $localeModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function removeLocale(Request $request, $id) {

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

        $localeModel = Locale::find($id);

        if ($localeModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $localeModel->delete();

        return response()->json([

        ]);
    }

    public function createLocale(Request $request) {

        $validator = Validator::make($request->all(), [
            'language_name' => 'string|min:1|max:255',
            'language_native' => 'string|min:1|max:255',
            'language_tag' => 'string|min:2|max:3'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $localeId = Uuid::uuid4();
        $localeTag = $request->input('language_tag');
        $localeName = $request->input('language_name');
        $localeNative = $request->input('language_native');

        $newLocaleModel = new Locale;
        $newLocaleModel->id = $localeId;
        $newLocaleModel->language_tag = $localeTag;
        $newLocaleModel->language_name = $localeName;
        $newLocaleModel->language_native = $localeNative;
        $newLocaleModel->save();

        return response()->json([
            'locale' => $newLocaleModel
        ], Response::HTTP_OK);
    }
}

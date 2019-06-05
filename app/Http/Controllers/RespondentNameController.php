<?php

namespace App\Http\Controllers;

use App\Services\RespondentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;
use Validator;

class RespondentNameController extends Controller {

    public function createRespondentName (RespondentService $respondentService, Request $request, $respondentId) {

        $respondentId = urldecode($respondentId);

        $validator = Validator::make([
            'localeId' => $request->get('locale_id'),
            'name' => $request->get('name'),
            'isDisplayName' => $request->get('is_display_name'),
            'respondentId' => $respondentId
        ], [
            'localeId' => 'nullable|string|min:36|exists:locale,id',
            'name' => 'required|string',
            'isDisplayName' => 'nullable|boolean',
            'respondentId' => 'required|string|min:36|exists:respondent,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $name = $respondentService->createRespondentName(
            $respondentId,
            $request->get('name'),
            $request->get('locale_id'),
            $request->get('is_display_name')
        );

        return response()->json([
            'name' => $name
        ], Response::HTTP_OK);
    }

    /**
     * Controller method for editing a respondent name
     * @param RespondentService $respondentService
     * @param Request $request
     * @param $respondentId
     * @param $respondentNameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function editRespondentName (RespondentService $respondentService, Request $request, $respondentId, $respondentNameId) {

        $respondentNameId = urldecode($respondentNameId);

        $validator = Validator::make([
            'respondentNameId' => $respondentNameId,
            'isDisplayName' => $request->get('is_display_name'),
            'localeId' => $request->get('locale_id'),
            'name' => $request->get('name')
        ], [
            'respondentNameId' => 'nullable|string|min:36|exists:respondent_name,id',
            'isDisplayName' =>'nullable|boolean',
            'localeId' => 'nullable|string',
            'name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $newName = $respondentService->editRespondentName(
            $respondentNameId,
            $request->get('name'),
            $request->get('locale_id'),
            $request->get('is_display_name')
        );

        return response()->json([
            'name' => $newName
        ], Response::HTTP_OK);

    }

    /**
     * Route for deleting a respondent name
     * @param RespondentService $respondentService
     * @param $respondentId
     * @param $respondentNameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRespondentName (RespondentService $respondentService, $respondentId, $respondentNameId) {
        $validator = Validator::make([
            'respondentNameId' => $respondentNameId
        ], [
            'respondentNameId' => 'required|string|min:36|exists:respondent_name,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }
        try {
            $respondentService->deleteRespondentName($respondentNameId);
        } catch (Throwable $e) {
            return response()->json([
                'msg' => $e
            ], Response::HTTP_BAD_REQUEST);
        }
        return response()->json([
            'msg' => "deleted $respondentNameId"
        ]);
    }

}
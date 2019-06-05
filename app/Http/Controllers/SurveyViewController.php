<?php
/**
 * Created by IntelliJ IDEA.
 * User: wi27
 * Date: 4/18/2018
 * Time: 3:02 PM
 */

namespace App\Http\Controllers;


class SurveyViewController
{
    public function showLogin ($formId) {
//        $validator = Validator::make([
//            'form_id' => $formId
//        ], [
//            'form_id' => 'required|string|min:36|exists:form,id'
//        ]);
//
//        if ($validator->fails()) {
//            return view('web-survey', ['msg' => 'invalid form id']);
//        }

        return view('web-survey', ['msg' => '']);
    }
}
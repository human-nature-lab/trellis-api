<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\QuestionGroup;
use App\Models\SectionQuestionGroup;
use App\Services\QuestionGroupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SectionQuestionGroupController extends Controller {

  public function updateSectionQuestionGroup (Request $request, String $id) {
    $validator = Validator::make($request->input(), [
      'section_id' => 'required|string|exists:section,id',
      'question_group_id' => 'required|string|exists:question_group,id',
      'question_group_order' => 'integer',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors(),
      ], Response::HTTP_BAD_REQUEST);
    }

    DB::transaction(function () use ($request, $id) {
      $sqg = SectionQuestionGroup::find($id);
      $sqg->section_id = $request->input('section_id');
      $sqg->question_group_order = $request->input('question_group_order');
      $sqg->save();

      // reorder the other members of the group
      $others = SectionQuestionGroup::where('section_id', $sqg->section_id)->
        where('id', '<>', $id)->
        orderBy('question_group_order')->
        get();
      for ($i = 0; $i < count($others); $i++) {
        if ($i < $request->input('question_group_order')) {
          $others[$i]->question_group_order = $i;
        } else {
          $others[$i]->question_group_order = $i + 1;
        }
        $others[$i]->save();
      }
      
    });
    
    return response()->json([
      'msg' => "success",
    ]);

  }

}
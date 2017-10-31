<?php

namespace App\Services;

use Log;
use App\Models\Form;
use App\Models\Datum;
use App\Models\Survey;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Models\Question;

class ExportService
{
    public static function createExport($formId)
    {
        
        // Get all of the form questions (including unanswered questions)
        $form = Form::find($formId);


        $headersMap = DB::table('question')
            ->join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('form', 'form_section.form_id', '=', 'form.id')
            ->where('form.id', '=', $form->id)
            ->lists('question.var_name','question.id');

        $headersMap['survey_id'] = 'survey_id';
        $headersMap['respondent_id'] = 'respondent_id';

        $allSurveys = DB::table('survey')
                        ->where('survey.form_id', '=', $form->id)
                        ->get();


        $rows = array();
        foreach ($allSurveys as $survey){
            Log::debug("survey $survey->id");
            $datums = DB::table('datum_choice')
                ->join('datum', 'datum.id', '=', 'datum_choice.datum_id')
                ->join('choice', 'datum_choice.choice_id', '=', 'choice.id')
                ->join('question', 'datum.question_id', '=', 'question.id')
                ->where('datum.survey_id', '=', $survey->id)
                ->get();
            $row = array();
            foreach ($datums as $datum){
                $row[$datum->question_id] = $datum->val;
//                Log::debug("key $datum->question_id, val $datum->val");
            }

            $row['survey_id'] = $survey->id;
            $row['respondent_id'] = $survey->respondent_id;

            array_push($rows, $row);
        }


        // TODO:


        // TODO: Format as csv string
        // TODO: Write file to disk
        $uuid = Uuid::uuid4();
        $fileName = "$uuid.csv";
        $filePath = storage_path() ."/app/". $fileName;

		ExportService::writeCsv($headersMap, $rows, $filePath);

		return $fileName;

    }


    public static function writeCsv($colMap, $rowMaps, $filePath){

    	// TODO: make sure that the columns line up correctly
    	$empty = 'n/a';

    	$headerIds = array();
    	$headerNames = array();
    	foreach ($colMap as $id => $name){
    	    Log::debug("colMap $id, $name");
    		array_push($headerIds, $id);
    		array_push($headerNames, $name);
    	}


     	$file = fopen($filePath, 'w');

     	// Write headers
     	fputcsv($file, $headerNames);
    	foreach ($rowMaps as $rowMap){
    		$row = array();
    		foreach ($headerIds as $id){
    			if(array_key_exists($id, $rowMap)){
	    			array_push($row, $rowMap[$id]);
	    		} else {
	    			array_push($row, $empty);
	    		}
    		}
    		// Write row
        	fputcsv($file, $row);
    	}
    	

        fclose($file);

    }

}

<?php
namespace App\Services;

use Log;
use App\Models\ReportFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Models\Datum;
use App\Services\FileService;
use App\Classes\Memoization;

class ReportService
{

    public static function saveImagesFile(&$report, &$images){
        // Make sure that each image id is unique
        $uniqueSieve = [];
        $images = array_filter($images, function($img) use ($uniqueSieve){
            $res = !array_key_exists($img->id, $uniqueSieve);
            $uniqueSieve['id'] = true;
            return $res;
        });

        // Map stdClass to associative array
        $images = array_map(function($image){
            return array(
                'id' => $image->id,
                'file_name' => $image->file_name
            );
        }, $images);

        return self::saveDataFile($report, array('id'=>'id', 'file_name'=>'file_name'), $images, 'image');
    }

    /**
     * Save the $headers and $rows data to a file and then add an entry in the report_file table.
     * @param $report - The report object returned from the report table
     * @param $headers - Column key => Column name associative array
     * @param $rows - Array of associative arrays for each row where row indexes match header keys
     * @param string $type - The type of the report_file entry
     * @return bool - Whether or not the file was created. The file won't be created if there aren't any headers or rows.
     */
    public static function saveDataFile($report, $headers, $rows, $type='data'){
        if(count($headers) === 0 || count($rows) === 0)
            return false;
        $csvReportFile = new ReportFile();
        $csvReportFile->id = Uuid::uuid4();
        $csvReportFile->report_id = $report->id;
        $csvReportFile->file_type = $type;
        $csvReportFile->file_name = $csvReportFile->id . '.csv';
        $filePath = storage_path("app/".$csvReportFile->file_name);
        FileService::writeCsv($headers, $rows, $filePath);
        $csvReportFile->save();

        return true;
    }


    /**
     * Save the meta file. This interperates the file headers based on unique values in each row.
     */
    public static function saveMetaFile($report, $rows){

        $headers = ['column'=>'column'];
        $newRows = [];
        foreach($rows as $column=>$row){
            $newRow = ['column'=>$column];
            foreach($row as $name=>$val){
                $headers[$name] = $name;
                $newRow[$name] = $val;
            }
            array_push($newRows, $newRow);
        }

        return self::saveDataFile($report, $headers, $newRows, 'meta');

    }


    /**
     * Zero padded numbers
     */
    public static function zeroPad($n){
        return str_pad($n + 1, 2, "0", STR_PAD_LEFT);
    }


    public static function getFormQuestions($formId, $locale){
        return DB::table('question')
            ->join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('form', 'form_section.form_id', '=', 'form.id')
            ->join('question_type', 'question.question_type_id', '=', 'question_type.id')
            ->leftJoin('translation_text', function($join) use ($locale) {
                $join->on('translation_text.translation_id', '=', 'question.question_translation_id');
                $join->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
            })
            ->where('form.id', '=', $formId)
            ->whereNull('question.deleted_at')
            ->select('question.id',
                'translation_text.translated_text as name',
                'question.var_name',
                'question_type.name as qtype',
                'form_section.follow_up_question_id')
            ->get();
    }

    public static function getFormSurveys($formId){
        return DB::table('survey')
            ->where('survey.form_id', '=', $formId)
            ->whereNull('survey.deleted_at')
            ->get();
    }

    public static function getRosterRows($surveyId, $questionId){
        $rows = DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->whereNull('datum.deleted_at')
            ->where('datum.val', 'not like', 'roster%')
            ->where('datum.question_id', '=', $questionId)
            ->get();

        return $rows;

    }

    public static function getQuestionDatum($surveyId, $questionId){
        return DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $questionId)
            ->get();
    }


    public static function handleDefault($surveyId, $question, $repeatString, $parentDatumId){

        $datum = self::firstDatum($surveyId, $question->id, $parentDatumId);
        $key = $question->id . $repeatString;
        $name = $question->var_name . $repeatString;

        $headers = [$key=>$name];
        $data = [];
        if($datum !== null && $datum->opt_out !== null) {
            $data[$key] = $datum->val;
        } else if($datum !== null){
            $data[$key] = $datum->val;
        }

        $metaData = [$headers[$key] => [
            'column' => $headers[$key],
            'question.name' => $question->name,
            'question.type' => $question->qtype,
            'question.var_name' => $question->var_name
        ]];

        return array($headers, $data, $metaData);

    }

    public static function handleMultiChoice($surveyId, $question, $repeatString, $useChoiceNames=false, $locale, $parentDatumId=null){
        $query = DB::table('datum')
            ->leftJoin('datum_choice', function($join){
                $join->on('datum_choice.datum_id', '=', 'datum.id');
                $join->whereNull('datum_choice.deleted_at');
            })
            ->leftJoin('choice', 'choice.id', '=', 'datum_choice.choice_id')
            ->leftJoin('translation_text', function($join) use ($locale) {
                $join->on('translation_text.translation_id', '=', 'choice.choice_translation_id');
                $join->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
            })
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $question->id)
            ->whereNull('datum.deleted_at')
            ->select('datum.val', 'datum.name', 'translation_text.translated_text as translated_val', 'datum.opt_out');

        if($parentDatumId){
            $query = $query->where('datum.parent_datum_id', '=', $parentDatumId);
        }

        $datum = $query->first();
        $key = $question->id . $repeatString;
        $name = $question->var_name . $repeatString;
        $headers = [$key=>$name];
        $data = [];
        $metaData[$headers[$key]] = [
            'column' => $headers[$key],
            'question.type' => $question->qtype,
            'question.name' => $question->name,
            'question.var_name' => $question->var_name
        ];
        if($datum !== null){
            if($datum->opt_out !== null){
                $data[$key] = $datum->opt_out;
            } else if($useChoiceNames){
                $data[$key] = $datum->translated_val;
            } else {
                $data[$key] = $datum->val;
            }
        }

        return [$headers, $data, $metaData];

    }

    public static function handleMultiSelect($surveyId, $question, $repeatString, $useChoiceNames=false, $locale, $parentDatumId){

        $headers = [];
        $data = [];
        $metaData = [];

        $parentDatum = ReportService::firstDatum($surveyId, $question->id, $parentDatumId);
        $possibleChoices = DB::table('question_choice')
            ->join('choice', 'choice.id', '=', 'question_choice.choice_id')
            ->leftJoin('translation_text', function($join) use ($locale)
            {
                $join->on('translation_text.translation_id', '=', 'choice.choice_translation_id')
                    ->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
            })
            ->where('question_choice.question_id', '=', $question->id)
            ->select('choice.val', 'choice.id', 'translation_text.translated_text as name');



        // Add headers for all possible choices
        foreach ($possibleChoices->get() as $choice){
            $key = $question->id . '___' . $repeatString . $choice->val;
            $headers[$key] = $question->var_name . $repeatString . '_' . $choice->val;
            $metaData[$headers[$key]] = [
                'column' => $headers[$key],
                'question.name' => $question->name,
                'question.var_name' => $question->var_name,
                'question.type' => $question->qtype,
                'choice.val' => $choice->val,
                'choice.id' => $choice->id,
                'choice.name' => $choice->name
            ];
        }

        // Add selected choices if there is a parent datum
        if($parentDatum !== null){
            $selectedChoices = DB::table('datum_choice')
                ->join('choice', 'datum_choice.choice_id', '=', 'choice.id')
                ->join('question_choice', 'choice.id', '=', 'question_choice.choice_id')
                ->leftJoin('translation_text', function($join) use ($locale)
                {
                    $join->on('translation_text.translation_id', '=', 'choice.choice_translation_id')
                        ->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
                })
                ->where('datum_choice.datum_id', '=', $parentDatum->id)
                ->select('choice.val', 'choice.id', 'translation_text.translated_text as name')
                ->get();

            // Add data for all selected choices
            foreach ($selectedChoices as $choice) {
                $key = $question->id . '___' . $repeatString . $choice->val;
                if($useChoiceNames){
                    $data[$key] = $choice->name;
                } else{
                    $data[$key] = true;
                }
            }
        }

        // Handle opted_out questions
        if($parentDatum !== null && $parentDatum->opt_out !== null){
            $key = $question->id . '___' . $repeatString;
            $headers[$key] = $question->var_name . $repeatString;
            $data[$key] = $parentDatum->opt_out;
        }

        return [$headers, $data, $metaData];

    }

    /**
     * Return first datum matching $surveyId and $questionId. Also matches $parentDatumId if it isn't null.
     * @param $surveyId
     * @param $questionId
     */
    public static function firstDatum($surveyId, $questionId, $parentDatumId=null){
        $query = DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $questionId)
            ->whereNull('datum.deleted_at');
        if($parentDatumId){
            $query = $query->where('datum.parent_datum_id', '=', $parentDatumId);
        }
        return $query->first();
    }

    public static function handleImage($surveyId, $question, $repeatString, $parentDatumId){
        $headers = [];
        $data = [];
        $images = [];
        $metaData = [];

        // This is flawed because it will use the same datum for 2 rows in a roster. The imageDatum needs to reference the
        // parent datum that is the roster row as well. This is likely flawed in all of these exports and will need to be
        // changed.
        $imageDatum = self::firstDatum($surveyId, $question->id, $parentDatumId);
        if($imageDatum !== null){
            $imageData = DB::table('datum_photo')
                ->join('photo', 'datum_photo.photo_id', '=', 'photo.id')
                ->where('datum_photo.datum_id', '=', $imageDatum->id)
                ->whereNull('datum_photo.deleted_at')
                ->select('photo.file_name', 'photo.id')
                ->get();
            // TODO: Check if you can omit the $imageDatum and maybe consider doing a semi-colon delimited lsit instead
            $imageList = [];
            foreach($imageData as $index => $datum){
                array_push($imageList, $datum->file_name);
                array_push($images, $datum);
            }
            $key = $question->id.$repeatString;
            $headers[$key] = $question->var_name.$repeatString;
            $data[$key] = implode(';', $imageList);
            $metaData[$headers[$key]] = [
                'column' => $headers[$key],
                'question.name' => $question->name,
                'question.type' => $question->qtype,
                'question.var_name' => $question->var_name
            ];
        }

        // handle opted out questions
        if($imageDatum !== null && $imageDatum->opt_out !== null){
            $key = $question->id.$repeatString;
            $data[$key] = $imageDatum->opt_out;
        }

        return [$headers, $data, $images, $metaData];
    }

    public static function handleGeo($surveyId, $question, $repeatString, $locale, $parentDatumId, $useAnyLocale=true){

        $headers = [];
        $data = [];
        $metaData = [];
        $geoDatum = self::firstDatum($surveyId, $question->id, $parentDatumId);

        if($geoDatum) {
            $geoData = DB::table('datum_geo')
                ->join('geo', 'datum_geo.geo_id', '=', 'geo.id')
                ->leftJoin('translation_text', function ($join) use ($locale) {
                    $join->on('translation_text.translation_id', '=', 'geo.name_translation_id')
                        ->whereNull('translation_text.deleted_at')
                        ->on('translation_text.locale_id', '=', DB::raw("'" . $locale . "'"));
                })->where('datum_geo.datum_id', '=', $geoDatum->id)
                ->whereNull('datum_geo.deleted_at')
                ->select('translation_text.translated_text as name', 'geo.id', 'geo.name_translation_id as tId');

            foreach ($geoData->get() as $index => $geo) {
                // Get the next geo
                if($geo->name === null && $useAnyLocale){
                    $geoTranslation = DB::table('translation_text')
                        ->where('translation_text.translation_id', '=', $geo->tId)
                        ->first();
                    $geo->name = $geoTranslation->translated_text;
                }
                foreach (['name', 'id'] as $name) {
                    $key = $question->id . $repeatString . '_g' . $index . '_' . $name;
                    $headers[$key] = $question->var_name . $repeatString . '_g' . ReportService::zeroPad($index) . '_' . $name;
                    $metaData[$headers[$key]] = [
                        'column' => $headers[$key],
                        'question.name' => $question->name,
                        'question.type' => $question->qtype,
                        'question.var_name' => $question->var_name
                    ];
                    $data[$key] = $geo->$name;
                }
            }
        }

        return [$headers, $data, $metaData];

    }


    public static function buildFormTree($questions){

        $tree = array();
        $treeMap = array();

        // Add the base level questions to the tree
        foreach ($questions as $q) {
            if ($q->follow_up_question_id === null) {
                $tree[$q->id] = [];
                $treeMap[$q->id] = &$tree[$q->id];
            }
        }


        // Iterate adding nested values until no more nested values are found
        $foundNested = true;
        while ($foundNested) {
            $foundNested = false;
            foreach ($questions as $q) {
                if (!array_key_exists($q->id, $treeMap)
                    && $q->follow_up_question_id !== null
                    && array_key_exists($q->follow_up_question_id, $treeMap)) {
                    $n = [];
                    $treeMap[$q->follow_up_question_id][$q->id] = &$n;
                    $treeMap[$q->id] = &$n;
                    $foundNested = true;
                }
            }
        }

        return array($tree, $treeMap);

    }


    /**
     * Create an csv file with one row per survey filled out for a single formId
     * @param $formId - Id of the form to export
     * @return string - The name of the file that was exported. The file is stored in 'storage/app'
     */
    public static function createFormReport($formId, $fileId, $config){

        $questions = ReportService::getFormQuestions($formId);

        $questionsMap = array_reduce($questions, function($agg, &$q){
            $agg[$q->id] = $q;
            return $agg;
        }, array());

        $defaultColumns = array(
            'id' => 'survey_id',
            'respondent_id' => 'respondent_id',
            'created_at' => 'created_at',
            'completed_at' => 'completed_at'
        );

        // 1. Create tree with follow up questions nested or if roster type then have the rows nested an follow up questions nested for each row
        // 2. Add any questions without follow ups
        // 3. Flatten the tree into a single

        list($tree, $treeMap) = ReportService::buildFormTree($questions);

        $headers = array();
        $rows = array();

        $surveys = ReportService::getFormSurveys($formId);

        foreach($surveys as $survey){

            list($surveyHeaders, $data) = ReportService::formatSurveyData($survey, $tree, $treeMap, $questionsMap);

            $headers = array_replace($headers, $surveyHeaders);

            // Add survey default values
            foreach($defaultColumns as $key=>$name){
                $data[$key] = $survey->$key;
            }

            array_push($rows, $data);

        }

        // Sort non default columns first then add default columns
        asort($headers);
        $headers = $defaultColumns + $headers; // add at the beginning of the array

        $filePath = storage_path() ."/app/". $fileId . '.csv';
        FileService::writeCsv($headers, $rows, $filePath);

    }

}

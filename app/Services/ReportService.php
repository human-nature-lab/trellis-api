<?php
namespace App\Services;

use App\Models\Report;
use App\Models\Study;
use App\Models\Translation;
use Exception;
use Log;
use App\Models\ReportFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class ReportService {


    public static function saveFileStream (Report $report, $fileName, $type = 'data') {
        $csvReportFile = new ReportFile();
        $csvReportFile->id = Uuid::uuid4();
        $csvReportFile->report_id = $report->id;
        $csvReportFile->file_type = $type;
        $csvReportFile->file_name = $fileName;
        $csvReportFile->save();
        return $csvReportFile;
    }

    /**
     * Get the correct localeId to use for a report
     * @param $config
     * @param String $studyId
     * @return mixed
     */
    public static function extractLocaleId ($config, $studyId) {
        if (isset($config) && (isset($config->localeId) || isset($config->locale))) {
            return isset($config->localeId) ? $config->localeId : $config->locale;
        } else if (isset($studyId)) {
            $study = Study::find($studyId);
            return $study->default_locale_id;
        } else {
            Log::error('Could not find valid localeId');
            return null;
        }
    }

    /**
     * Convert the text into a safe version of the string. For headers.
     * @param String $text
     * @return mixed|String
     */
    public static function makeTextSafe (String $text) {
        $text = str_replace(' ', '', $text);
        $text = strtolower($text);
        return preg_replace('/[^a-z0-9_]/im', '_', $text);
    }

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
    public static function saveDataFile($report, $headers, &$rows, $type='data'){
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
    public static function saveMetaFile($report, &$rows){

        $headers = ['header'=>'header'];
        $newRows = [];
        foreach($rows as $column=>$row){
            $newRow = ['header'=>$column];
            foreach($row as $name=>$val){
                $headers[$name] = strtolower($name);
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


    public static function getQuestionChoices($question, $locale) {
        $possibleChoices = DB::table('question_choice')
            ->join('choice', 'choice.id', '=', 'question_choice.choice_id')
            ->leftJoin('translation_text', function($join) use ($locale)
            {
                $join->on('translation_text.translation_id', '=', 'choice.choice_translation_id')
                    ->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
            })
            ->where('question_choice.question_id', '=', $question->id)
            ->whereNull('question_choice.deleted_at')
            ->select('choice.val', 'choice.id', 'translation_text.translated_text as name')
            ->get();
        $translations = DB::table('translation_text')
            ->join('choice', 'choice.choice_translation_id', '=', 'translation_text.translation_id')
            ->join('question_choice', 'question_choice.choice_id', '=', 'choice.id')
            ->join('locale', 'locale.id', '=', 'translation_text.locale_id')
            ->whereNull('question_choice.deleted_at')
            ->where('question_choice.question_id', '=', $question->id)
            ->select('choice.val', 'choice.id as id', 'translation_text.translated_text', 'locale.id as locale_id', 'locale.language_name')
            ->get();
        $choiceTranslations = [];
        foreach ($translations as $t) {
            if (!isset($choiceTranslations[$t->id])) {
                $choiceTranslations[$t->id] = [];
            }
            array_push($choiceTranslations[$t->id], $t);
        }

        foreach ($possibleChoices as $c) {
            $c->translations = $choiceTranslations[$c->id];
        }
        return $possibleChoices;
    }


    public static function getFormQuestions($formId, $locale){
        $translations = DB::table('translation_text')
            ->join('question', 'question.question_translation_id', '=', 'translation_text.translation_id')
            ->join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('form', 'form_section.form_id', '=', 'form.id')
            ->join('locale', 'locale.id', '=', 'translation_text.locale_id')
            ->where('form.id', '=', $formId)
            ->whereNull('question.deleted_at')
            ->select('question.id', 'translation_text.translated_text', 'locale.language_tag', 'locale.language_name')
            ->get();
        $questionTranslations = [];
        foreach ($translations as $t) {
            if (!isset($questionTranslations[$t->id])) {
                $questionTranslations[$t->id] = [];
            }
            array_push($questionTranslations[$t->id], $t);
        }
        $questions = DB::table('question')
            ->join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('form', 'form_section.form_id', '=', 'form.id')
            ->join('question_type', 'question.question_type_id', '=', 'question_type.id')
            ->leftJoin('translation_text', function($join) use ($locale) {
                $join->on('translation_text.translation_id', '=', 'question.question_translation_id');
                $join->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
            })
            ->leftJoin('locale', 'locale.id', '=', 'translation_text.locale_id')
            ->where('form.id', '=', $formId)
            ->whereNull('question.deleted_at')
            ->select('question.id',
                'translation_text.translated_text as name',
                'locale.id as locale_id',
                'locale.language_name as language_name',
                'question.var_name',
                'question_type.name as qtype',
                'form_section.follow_up_question_id')
            ->get();
        foreach ($questions as $q) {
             $q->translations = $questionTranslations[$q->id];
        }
        return $questions;
    }

    /**
     * Get the correct text for a translation.
     * @param Translation $translation
     * @param String $localeId
     * @return null
     */
    public static function translationToText (Translation $translation, String $localeId = null) {
        if (!isset($translation->translationText) || $translation->translationText->count() === 0) {
            return '[No text for this translation]';
        }
        $text = null;
        if ($localeId) {
            foreach ($translation->translationText as $tt) {
                if ($tt->locale_id === $localeId) {
                    $text = $tt->translated_text;
                }
            }
        }

        if (is_null($text) || strlen($text) === 0) {
            $tt = $translation->translationText[0];
            $locale = $tt->locale;
            $languageTag = $locale->language_tag;
            $text = $tt->translated_text . " ($languageTag)";
        }

        return $text;
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
            ->orderBy('sort_order', 'asc')
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

        $parentDatum = self::firstDatum($surveyId, $question->id, $parentDatumId);
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


 }

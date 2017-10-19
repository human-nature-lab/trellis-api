<?php

namespace App\Http\Controllers;

use app\Library\TranslationHelper;
use App\Http\Controllers\SectionController;
use App\Models\Choice;
use App\Models\Form;
use App\Models\FormSection;
use App\Models\Parameter;
use App\Models\Question;
use App\Models\QuestionAssignConditionTag;
use App\Models\QuestionChoice;
use App\Models\QuestionGroup;
use App\Models\QuestionParameter;
use App\Models\Section;
use App\Models\SectionQuestionGroup;
use App\Models\Study;
use App\Models\StudyForm;
use App\Models\Translation;
use App\Models\TranslationText;
use App\Services\FormService;
use App\Services\SectionService;
use App\Services\QuestionGroupService;
use App\Services\QuestionService;
use App\Services\QuestionChoiceService;
use App\Services\QuestionTypeService;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Csv\Reader;

class FormController extends Controller
{
    public function getForm(Request $request, $id)
    {
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

        //$formModel = Form::find($id);
        $formModel = Form::with('sections', 'nameTranslation')->find($id);

        if ($formModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'form' => $formModel
        ], Response::HTTP_OK);
    }

    public function importForm(Request $request, $studyId, FormService $formService, SectionService $sectionService, QuestionGroupService $questionGroupService, QuestionService $questionService, QuestionChoiceService $questionChoiceService, QuestionTypeService $questionTypeService)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId
        ]), [
            'studyId' => 'required|string|min:36|exists:study,id',
            'formName' => 'required|string',
            'formType' => 'required|integer'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $formName = $request->input('formName');
        $formType = $request->input('formType');

        // Create the form
        //$newForm = $formService->createForm($formName, $studyId, $formType);

        // TODO: remove this hard-coded file system location
        //$adapter = new Local(storage_path() . '/form-import');
        //$filesystem = new Filesystem($adapter);

        $hasFormFile = $request->hasFile('formJsonFile');
        if ($hasFormFile) {
            $formFile = $request->file('formJsonFile');
            $formFileStream = fopen($formFile->getRealPath(), 'r+');
            $formJsonString = stream_get_contents($formFileStream);
            $formObject = json_decode($formJsonString, true);
            $testForm = new Form;
            $testForm->fill($formObject);

            return response()->json(
                [ 'testForm' => $formObject ],
                Response::HTTP_OK
            );
        } else {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Provide a JSON file exported from Trellis'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function importSection(Request $request, $formId, SectionService $sectionService, QuestionGroupService $questionGroupService, QuestionService $questionService, QuestionChoiceService $questionChoiceService, QuestionTypeService $questionTypeService)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'formId' => $formId
        ]), [
            'formId' => 'required|string|min:36|exists:form,id',
            'form_import_section_name' => 'required|string',
            'sort_order' => 'required|integer'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        // Create the section
        $newSection = $sectionService->createSection($formId, $request->input('form_import_section_name'), $request->input('sort_order'));

        // TODO: remove this hard-coded file system location
        $adapter = new Local(storage_path() . '/form-import');
        $filesystem = new Filesystem($adapter);

        $hasQuestionFile = $request->hasFile('questionFile');
        $hasChoiceFile = $request->hasFile('choiceFile');
        if ($hasQuestionFile and $hasChoiceFile) {
            // Detect Line Endings
            if (!ini_get("auto_detect_line_endings")) {
                ini_set("auto_detect_line_endings", '1');
            }

            $questionFile = $request->file('questionFile');
            $questionStream = fopen($questionFile->getRealPath(), 'r+');
            $questionExtension = $questionFile->getClientOriginalExtension();
            $questionName = Uuid::uuid4();
            $questionFileName = $questionName . '.' . $questionExtension;
            $filesystem->writeStream($questionFileName, $questionStream);
            fclose($questionStream);

            $questionMap = array();
            $questionCsv = Reader::createFromPath(storage_path() . '/form-import/' . $questionFileName);
            //$questionCsv->setDelimiter("\t");
            $questionHeaderMap = array();
            $questionHeaders = $questionCsv->fetchOne();
            $localeIndexArray = array();

            foreach ($questionHeaders as $i=>$questionHeader) {
                if (strncmp($questionHeader, "question_text_", 14) == 0) {
                    $localeIndexArray[substr($questionHeader, 14)] = $i;
                }
                $questionHeaderMap[$questionHeader] = $i;
            }

            // \Log::info('$questionHeaderMap: ' . implode(" ", array_keys($questionHeaderMap)));
            // Skip past header-row
            $questionCsv->setOffset(1);
            $questionCsv->each(function ($row) use ($questionGroupService, $questionService, $questionTypeService, $newSection, &$questionMap, $localeIndexArray, $questionHeaderMap) {
                // Create a question group
                $newQuestionGroup = $questionGroupService->createQuestionGroup($newSection->id);
                $textLocaleArray = array();
                foreach ($localeIndexArray as $localeTag=>$i) {
                    $textLocaleArray[$localeTag] = $row[$i];
                }
                // Create a question
                $questionType = $row[$questionHeaderMap['question_type']];
                $questionTypeId = $questionTypeService->getIdByName($questionType);
                $questionVarName = $row[$questionHeaderMap['question_var_name']];

                // \Log::info('$textLocaleArray: ' . implode(" ", $textLocaleArray));
                $newQuestion = $questionService->createQuestionLocalized($textLocaleArray, $questionTypeId, $newQuestionGroup->id, $questionVarName);
                //\Log::info('$questionVarName: ' . $questionVarName);
                //\Log::info('$newQuestion->id ' . $newQuestion->id);
                $questionMap[$questionVarName] = $newQuestion->id;
                return true;
            });

            //\Log::info('$questionMap: ' . implode(" ", $questionMap));

            $choiceFile = $request->file('choiceFile');
            $choiceStream = fopen($choiceFile->getRealPath(), 'r+');
            $choiceExtension = $choiceFile->getClientOriginalExtension();
            $choiceName = Uuid::uuid4();
            $choiceFileName = $choiceName . '.' . $choiceExtension;
            $filesystem->writeStream($choiceFileName, $choiceStream);
            fclose($choiceStream);

            $choiceCsv = Reader::createFromPath(storage_path() . '/form-import/' . $choiceFileName);
            //$choiceCsv->setDelimiter("\t");
            $choiceHeaderMap = array();
            $choiceHeaders = $choiceCsv->fetchOne();
            $localeIndexArray = array();
            foreach ($choiceHeaders as $i=>$choiceHeader) {
                if (strncmp($choiceHeader, "choice_text_", 12) == 0) {
                    $localeIndexArray[substr($choiceHeader, 12)] = $i;
                }
                $choiceHeaderMap[$choiceHeader] = $i;
            }
            // Skip past header-row
            $choiceCsv->setOffset(1);
            $choiceCsv->each(function ($row) use ($localeIndexArray, $questionChoiceService, $choiceHeaderMap, $questionMap) {
                // Create a question group
                $textLocaleArray = array();
                foreach ($localeIndexArray as $localeTag=>$i) {
                    $textLocaleArray[$localeTag] = $row[$i];
                }

                // Create a QuestionChoice
                $val = $row[$choiceHeaderMap['choice_val']];
                $sortOrder = $row[$choiceHeaderMap['choice_sort_order']];
                $questionVarName =  $row[$choiceHeaderMap['question_var_name']];
                $questionId = $questionMap[$questionVarName];
                $newQuestionChoice = $questionChoiceService->createQuestionChoiceLocalized($val, $textLocaleArray, $sortOrder, $questionId);
                return true;
            });

            $returnSection = Form::find($formId)
                ->sections()
                ->find($newSection->id);
            return response()->json(
                [ 'section' => $returnSection ],
                Response::HTTP_OK
            );
        } else {
            return response()->json([
                'msg' => 'Request failed',
                'err' => 'Provide a CSV file for questions and choices'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllForms(Request $request)
    {
        $formModel = Form::get();

        return response()->json(
            ['forms' => $formModel],
            Response::HTTP_OK
        );
    }

    public function getAllStudyForms(Request $request, $studyId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId
        ]), [
            'studyId' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }


        $studyModel = Study::find($studyId);
        $formModel = $studyModel->forms()->get();

        //$censusFormModel = Form::with('nameTranslation')->where('id', $studyModel->census_form_master_id)->get();

        /*
        $formModel = Form::select('form.id', 'form.form_master_id', 'form.version', 'form.is_published', 'tt.translated_text AS name')
            ->join('translation_text AS tt', 'tt.translation_id', '=', 'form.name_translation_id')
            ->join('study_form AS sf', 'sf.form_master_id', '=', 'form.form_master_id')
            ->where('sf.study_id', $studyId)
            ->where('tt.locale_id', $localeId)
            ->get();
        */

        return response()->json(
            ['forms' => $formModel],
            Response::HTTP_OK
        );
    }

    public function updateForm(Request $request, $id)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'required|string|min:36',
            'form_master_id' => 'string|min:36',
            'name_translation_id' => 'string|min:36'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $formModel = Form::find($id);

        if ($formModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $formModel->fill->input();
        $formModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function publishForm(Request $request, $form_master_id)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'form_master_id' => $form_master_id
        ]), [
            'form_master_id' => 'string|min:36|exists:form,form_master_id',
            'published' => 'required|integer|min:0|max:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $published = $request->input('published');

        // DB::enableQueryLog();
        Form::where('form_master_id', $form_master_id)
            ->update(['is_published' => $published]);

        // \Log::info('publishFormQuery: ' . json_encode(DB::getQueryLog()));
        // DB::disableQueryLog();

        return response()->json([

        ]);
    }

    public function removeForm($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36|exists:form,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $formModel = Form::find($id);

        if ($formModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $formModel->delete();

        return response()->json([

        ]);
    }

    public function createForm(Request $request, FormService $formService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'study_id' => 'required|string|min:36|exists:study,id',
            'form_type' => 'integer|min:0|max:255'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $formName = ($request->input('name') == null) ? "" : $request->input('name');
        $studyId = $request->input('study_id');

        $newFormModel = $formService->createForm(
            $formName,
            $studyId,
            $request->input('form_type')
        );

        if ($newFormModel === null) {
            return response()->json([
                'msg' => 'Form creation failed.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        //$returnForm = Form::with('nameTranslation')->find($newFormModel->id);
        $returnForm = Study::find($studyId)->forms()->find($newFormModel->id);

        return response()->json([
            'form' => $returnForm
        ], Response::HTTP_OK);
    }

    /*
    public function createCensusForm(Request $request, FormService $formService) {

        $validator = Validator::make($request->all(), [
            'study_id' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $newFormModelId = Uuid::uuid4();

        $newFormModel = $formService->createCensusForm(
            $request->input('name'),
            $request->input('study_id'),
            $newFormModelId
        );

        if ($newFormModel === null) {
            return response()->json([
                'msg' => 'Form creation failed.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $returnForm = Form::with('nameTranslation')->find($newFormModelId);

        return response()->json([
            'form' => $returnForm
        ], Response::HTTP_OK);
    }
    */

    public function editFormPrep(Request $request, $studyId, $formId, $formMasterId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId,
            'formId' => $formId,
            'formMasterId' => $formMasterId,
            'isPublished' => $request->input('is_published')
        ]), [
            'studyId' => 'required|string|min:36|exists:study,id',
            'formId' => 'required|string|min:36|exists:form,id',
            'formMasterId' => 'required|string|min:36|exists:form,id',
            'isPublished' => 'required|boolean:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        // Check for an unpublished, new version of this form.
        $formNewModel = Form::where('form_master_id', $formMasterId)
            ->where('is_published', 0)
            ->orderBy('version', 'desc')
            ->first();

        // Get the latest version of this form.
        $formModel = Form::where('form_master_id', $formMasterId)
            ->where('is_published', 1)
            ->orderBy('version', 'desc')
            ->first();

        if ($formNewModel !== null && $formModel->id === $formNewModel->id) {
            return response()->json([
                'msg' => 'A version of this form is already being edited.'
            ], Response::HTTP_CONFLICT);
        }

        // Copy the form's data to a new form.

        DB::transaction(function () use ($request, $formModel, $studyId, $formId, $formMasterId) {

            // We need the locale ID so we grab the current Study.
            $studyModel = Study::where('id', $studyId)
                ->get();

            // Now we need the TranslationText models for this form's title.


            // Now we need to create a new Translation.
            $newFormTranslationModel = new Translation;

            $newFormTranslationModelId = Uuid::uuid4();

            $newFormTranslationModel->id = $newFormTranslationModelId;

            $newFormTranslationModel->save();

            // We now need to create the TranslationTexts.
            $translationTextModels = TranslationText::where('translation_id', $formModel->name_translation_id)
                ->get();

            // We have to loop over them and recreate them again.
            foreach ($translationTextModels as $translationTextModel) {
                $translationTextModelId = Uuid::uuid4();
                $translationTextModelTranslationId = $newFormTranslationModelId;
                $translationTextModelLocaleId = $translationTextModel->locale_id;
                $translationTextModelTranslatedText = $translationTextModel->translated_text;

                $newTranslationTextModel = new TranslationText;

                $newTranslationTextModel->id = $translationTextModelId;
                $newTranslationTextModel->translation_id = $translationTextModelTranslationId;
                $newTranslationTextModel->locale_id = $translationTextModelLocaleId;
                $newTranslationTextModel->translated_text = $translationTextModelTranslatedText;

                $newTranslationTextModel->save();
            }

            // Now we have the translation_text model so we can create a new form using this form's data.
            $newFormModel = new Form;

            $newFormId = Uuid::uuid4();
            $newFormVersion = $formModel->version;

            $newFormVersion = intval($newFormVersion);
            $newFormVersion++;

            $newFormModel->id = $newFormId;
            $newFormModel->form_master_id = $formMasterId;
            $newFormModel->name_translation_id = $newFormTranslationModelId;
            $newFormModel->version = $newFormVersion;
            $newFormModel->is_published = 0;

            $newFormModel->save();

            // Now we need to start duplicating all of the Form components.
            // The Conditions are first.


            // The Sections are next.
            $sectionModels = Section::select('section.*')
                ->join('form_section AS fs', 'fs.section_id', '=', 'section.id')
                ->where('fs.form_id', $formId)
                ->get();

            // We now have a collection of Sections and need to recreate them for our new Form version.
            foreach ($sectionModels as $sectionModel) {
                // Set $sectionModel attributes to local variables.
                $sectionId = $sectionModel->id;
                $sectionNameTranslationId = $sectionModel->name_translation_id;

                // Create a new Section, generate a UUID.
                $newSectionModel = new Section;

                $newSectionModelId = Uuid::uuid4();

                // We now need to copy ALL of the TranslationTexts for this Section name.
                // Create a new Translation, generate a UUID.
                $newSectionTranslationModel = new Translation;

                $newSectionTranslationModelId = Uuid::uuid4();

                $newSectionTranslationModel->id = $newSectionTranslationModelId;

                $newSectionTranslationModel->save();

                // Now that we have our new Translation, we need to generate new TranslationTexts for each.
                // First we need ALL of the TranslationText models for this Translation.
                $sectionTranslationTextsModels = TranslationText::where('translation_id', $sectionNameTranslationId)
                    ->get();

                // Now we need to loop over the TranslationTexts and create a new one for each.
                foreach ($sectionTranslationTextsModels as $sectionTranslationTextModel) {
                    // Create a new TranslationText, generate a UUID.
                    $newTranslationTextModel = new TranslationText;

                    $newTranslationTextModelId = Uuid::uuid4();

                    // And we copy over the locale_id and the translated_text from the original.
                    $newTranslationTextModel->id = $newTranslationTextModelId;
                    $newTranslationTextModel->translation_id = $newSectionTranslationModelId;
                    $newTranslationTextModel->locale_id = $sectionTranslationTextModel->locale_id;
                    $newTranslationTextModel->translated_text = $sectionTranslationTextModel->translated_text;

                    $newTranslationTextModel->save();
                }

                // Continue creating a new Section
                $newSectionModel->id = $newSectionModelId;
                $newSectionModel->name_translation_id = $newSectionTranslationModelId;

                $newSectionModel->save();

                // We need to get our current FormSection to create a new one.
                $formSectionModel = FormSection::where('form_id', $formId)
                    ->where('section_id', $sectionModel->id)
                    ->first();

                $formSectionId = $formSectionModel->id;
                $formSectionFormid = $formSectionModel->form_id;
                $formSectionSectionId = $formSectionModel->section_id;
                $formSectionSortOrder = $formSectionModel->sort_order;
                $formSectionIsRepeatable = $formSectionModel->is_repeatable;
                $formSectionMaxRepetitions = $formSectionModel->max_repetitions;
                $formSectionRepeatPromptTranslationId = $formSectionModel->repeat_prompt_translation_id;

                // Now we need to link this new Section to the Form by creating a new FormSection.
                $newFormSectionModel = new FormSection;

                $newFormSectionModelId = Uuid::uuid4();

                $newFormSectionModel->id = $newFormSectionModelId;
                $newFormSectionModel->form_id = $newFormId;
                $newFormSectionModel->section_id = $newSectionModelId;
                $newFormSectionModel->sort_order = $formSectionSortOrder;
                $newFormSectionModel->is_repeatable = $formSectionIsRepeatable;
                $newFormSectionModel->max_repetitions = $formSectionMaxRepetitions;

                // We need to create a new Translation for this repeat_prompt_translation_id, as well as a UUID.
                $newRepeatPromptTranslationIdModel = new Translation;

                $newRepeatPromptTranslationIdModelId = Uuid::uuid4();

                $newRepeatPromptTranslationIdModel->id = $newRepeatPromptTranslationIdModelId;
                $newRepeatPromptTranslationIdModel->save();

                // Now that we have our new Translation, we need to generate new TranslationTexts for each.
                // First we need ALL of the TranslationText models for this Translation.
                $repeatPromptTranslationTextsModels = TranslationText::where('translation_id', $formSectionRepeatPromptTranslationId)
                    ->get();

                // Now we need to loop over the TranslationTexts and create a new one for each.
                foreach ($repeatPromptTranslationTextsModels as $repeatPromptTranslationTextsModel) {
                    // Create a new TranslationText, generate a UUID.
                    $newTranslationTextModel = new TranslationText;

                    $newTranslationTextModelId = Uuid::uuid4();

                    // And we copy over the locale_id and the translated_text from the original.
                    $newTranslationTextModel->id = $newTranslationTextModelId;
                    $newTranslationTextModel->translation_id = $newRepeatPromptTranslationIdModelId;
                    $newTranslationTextModel->locale_id = $repeatPromptTranslationTextsModel->locale_id;
                    $newTranslationTextModel->translated_text = $repeatPromptTranslationTextsModel->translated_text;

                    $newTranslationTextModel->save();
                }

                // Continue creating a new FormSection.
                $newFormSectionModel->repeat_prompt_translation_id = $newRepeatPromptTranslationIdModelId;

                $newFormSectionModel->save();

                // Now that we've finished creating our Section and FormSection, we need to create this Section's
                // associated QuestionGroup, ConditionTag, and SectionQuestionGroup.
                // First the QuestionGroups. Let's find the QuestionGroups associated with this Section.
                $questionGroupModels = QuestionGroup::select('question_group.*', 'sqg.question_group_order')
                    ->join('section_question_group AS sqg', 'sqg.question_group_id', '=', 'question_group.id')
                    ->join('form_section AS fs', 'fs.section_id', '=', 'sqg.section_id')
                    ->where('fs.form_id', $formId)
                    ->where('fs.section_id', $sectionId)
                    ->get();

                // Now we need to loop over the QuestionGroupModels and create a new one for each.
                foreach ($questionGroupModels as $questionGroupModel) {
                    $questionGroupId = $questionGroupModel->id;

                    //Create a new QuestionGroup
                    $newQuestionGroupModel = new QuestionGroup;

                    $newQuestionGroupModelId = Uuid::uuid4();

                    $newQuestionGroupModel->id = $newQuestionGroupModelId;

                    $newQuestionGroupModel->save();

                    // Now we need to create the SectionQuestionGroup for each of the QuestionGroups we're making.
                    $newSectionQuestionGroup = new SectionQuestionGroup;

                    $newSectionQuestionGroupId = Uuid::uuid4();

                    $newSectionQuestionGroup->id = $newSectionQuestionGroupId;
                    $newSectionQuestionGroup->section_id = $newSectionModelId;
                    $newSectionQuestionGroup->question_group_id = $newQuestionGroupModelId;
                    $newSectionQuestionGroup->question_group_order = $questionGroupModel->question_group_order;

                    $newSectionQuestionGroup->save();

                    // Now that we've finished creating our QuestionGroup, we need to create each of the questions.
                    // We're getting all of the Questions for this form.
                    $questionModels = Question::select('question.*', 'qt.name AS question_type_name')
                        ->join('section_question_group AS sqg', 'sqg.question_group_id', '=', 'question.question_group_id')
                        ->join('question_type AS qt', 'qt.id', '=', 'question.question_type_id')
                        ->where('sqg.section_id', $sectionId)
                        ->where('sqg.question_group_id', $questionGroupId)
                        ->get();

                    // Now we need to loop over each Question and create a new copy.
                    foreach ($questionModels as $questionModel) {
                        $questionModelId = $questionModel->id;
                        $questionModelTypeId = $questionModel->question_type_id;
                        $questionModelTranslationId = $questionModel->question_translation_id;
                        $questionModelGroupId = $questionModel->question_group_id;
                        $questionModelSortOrder = $questionModel->sort_order;
                        $questionModelVarName = $questionModel->var_name;

                        $newQuestionModel = new Question;

                        $newQuestionModelId = Uuid::uuid4();

                        $newQuestionModel->id = $newQuestionModelId;
                        $newQuestionModel->question_type_id = $questionModel->question_type_id;

                        // Create a new Translation for this QuestionModel
                        $newQuestionModelTranslation = new Translation;

                        $newQuestionModelTranslationId = Uuid::uuid4();

                        $newQuestionModelTranslation->id = $newQuestionModelTranslationId;

                        $newQuestionModelTranslation->save();

                        // Here we're grabbing all of the TranslationTexts for this Translation.
                        $questionTranslationTextsModels = TranslationText::where('translation_id', $questionModelTranslationId)
                            ->get();

                        // Now we need to loop over all the TranslationTexts for this Translation and create new TranslationTexts.
                        foreach ($questionTranslationTextsModels as $questionTranslationTextModel) {
                            $newQuestionTranslationTextModel = new TranslationText;

                            $newQuestionTranslationTextModelId = Uuid::uuid4();

                            $newQuestionTranslationTextModel->id = $newQuestionTranslationTextModelId;
                            $newQuestionTranslationTextModel->translation_id = $newQuestionModelTranslationId;
                            $newQuestionTranslationTextModel->locale_id = $questionTranslationTextModel->locale_id;
                            $newQuestionTranslationTextModel->translated_text = $questionTranslationTextModel->translated_text;

                            $newQuestionTranslationTextModel->save();
                        }

                        // Continue saving the Question model.
                        $newQuestionModel->question_translation_id = $newQuestionModelTranslationId;
                        $newQuestionModel->question_group_id = $newQuestionGroupModelId;
                        $newQuestionModel->sort_order = $questionModelSortOrder;
                        $newQuestionModel->var_name = $questionModelVarName;

                        $newQuestionModel->save();

                        // Now we need to go through each QuestionType and copy the variables associated.
                        // First we'll start with QuestionType === 'multiple_choice' || 'multiple_select'
                        if ($questionModel->question_type_name === 'multiple_select' || $questionModel->question_type_name === 'multiple_choice') {
                            $questionChoiceModels = QuestionChoice::where('question_id', $questionModelId)
                                ->get();

                            // We need to loop over the QuestionChoices to create new ones.
                            foreach ($questionChoiceModels as $questionChoiceModel) {
                                $questionChoiceId = $questionChoiceModel->id;
                                $questionChoiceQuestionId = $questionChoiceModel->question_id;
                                $questionChoiceChoiceId = $questionChoiceModel->choice_id;
                                $questionChoiceSortOrder = $questionChoiceModel->sort_order;

                                $newQuestionChoiceModel = new QuestionChoice;

                                $newQuestionChoiceModelId = Uuid::uuid4();

                                $newQuestionChoiceModel->id = $newQuestionChoiceModelId;
                                $newQuestionChoiceModel->question_id = $newQuestionModelId;

                                // Now we need to get the Choice for this QuestionChoice.
                                $choiceModel = Choice::where('choice.id', $questionChoiceChoiceId)
                                    ->first();

                                $newChoiceModel = new Choice;

                                $newChoiceModelId = Uuid::uuid4();

                                $newChoiceModel->id = $newChoiceModelId;

                                // We need to create a new Translation for this Choice.
                                $newChoiceTranslationModel = new Translation;

                                $newChoiceTranslationModelId = Uuid::uuid4();

                                $newChoiceTranslationModel->id = $newChoiceTranslationModelId;

                                $newChoiceTranslationModel->save();

                                // We need to get all of the TranslationTexts for this Translation id.
                                $translationTextModels = TranslationText::where('translation_id', $choiceModel->choice_translation_id)
                                    ->get();

                                // We loop over all the TranslationTexts to create new ones.
                                foreach ($translationTextModels as $translationTextModel) {
                                    $newTranslationTextModel = new TranslationText;

                                    $newTranslationTextModelId = Uuid::uuid4();

                                    $newTranslationTextModel->id = $newTranslationTextModelId;
                                    $newTranslationTextModel->translation_id = $newChoiceTranslationModelId;
                                    $newTranslationTextModel->locale_id = $translationTextModel->locale_id;
                                    $newTranslationTextModel->translated_text = $translationTextModel->translated_text;

                                    $newTranslationTextModel->save();
                                }

                                // We'll now continue the Choice creation.
                                $newChoiceModel->choice_translation_id = $newChoiceTranslationModelId;
                                $newChoiceModel->val = $choiceModel->val;

                                $newChoiceModel->save();
                            } // ---> Question Choices
                        } // ---> Question Types

                        // Now we need to copy all of the QuestionParameters for this Question.
                        // First we'll get all of the old QuestionParameters for this Question.
                        $questionParameterModels = QuestionParameter::where('question_id', $questionModelId)
                            ->get();

                        // Then we'll loop over them.
                        foreach ($questionParameterModels as $questionParameterModel) {
                            $questionParameterId = $questionParameterModel->id;
                            $questionParameterQuestionId = $questionParameterModel->question_id;
                            $questionParameterParameterId = $questionParameterModel->parameter_id;
                            $questionParameterVal = $questionParameterModel->val;

                            $newQuestionParameterModel = new QuestionParameter;

                            $newQuestionParameterModelId = Uuid::uuid4();

                            $newQuestionParameterModel->id = $newQuestionParameterModelId;
                            $newQuestionParameterModel->question_id = $newQuestionModelId;
                            $newQuestionParameterModel->parameter_id = $questionParameterParameterId;
                            $newQuestionParameterModel->val = $questionParameterVal;

                            $newQuestionParameterModel->save();
                        }
                        $questionAssignConditionTagModels = QuestionAssignConditionTag::where('question_id', $questionModelId)
                            ->get();

                        foreach ($questionAssignConditionTagModels as $questionAssignConditionTagModel) {
                            $newQuestionAssignConditionTagModel = new QuestionAssignConditionTag;

                            $newQuestionAssignConditionTagModelId = Uuid::uuid4();

                            $newQuestionAssignConditionTagModel->id = $newQuestionAssignConditionTagModelId;
                            $newQuestionAssignConditionTagModel->question_id = $newQuestionModelId;
                            $newQuestionAssignConditionTagModel->assign_condition_tag_id = $questionAssignConditionTagModel->assign_condition_tag_id;

                            $newQuestionAssignConditionTagModel->save();
                        }
                    } // ---> Questions
                } // ---> Question Groups
            } // ---> Sections
        }); // ---> DB Transaction

        return response()->json([
            'msg' => 'Form prepped successfully.'
        ], Response::HTTP_OK);
    }
}

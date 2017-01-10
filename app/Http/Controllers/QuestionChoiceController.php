<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use App\Models\QuestionParameter;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Choice;
use App\Models\Translation;
use App\Models\TranslationText;
use DB;
use Log;

class QuestionChoiceController extends Controller
{
    public function createNewQuestionChoice(Request $request, $questionId) {

        $validator = Validator::make(array_merge($request->all(), [
            'question_id' => $questionId]), [
            'question_id' => 'required|string|min:36|exists:question,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $questionChoiceId = Uuid::uuid4();
        $choiceId = Uuid::uuid4();
        $translationId = Uuid::uuid4();
        $newQuestionChoiceModel = new QuestionChoice;

        DB::transaction(function() use($questionChoiceId, $choiceId, $translationId, $questionId, $newQuestionChoiceModel) {

            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            $newChoiceModel = new Choice;
            $newChoiceModel->id = $choiceId;
            $newChoiceModel->choice_translation_id = $translationId;
            $newChoiceModel->val = "";
            $newChoiceModel->save();

            // Get the next available sort_order for the question, this should avoid race conditions as it is within the same transaction
            $sort_order = DB::select('select (ifnull((max(sort_order) + 1), 1)) as sort_order from question_choice where question_id = ?', [$questionId]);
            $newQuestionChoiceModel->id = $questionChoiceId;
            $newQuestionChoiceModel->question_id = $questionId;
            $newQuestionChoiceModel->choice_id = $choiceId;
            $newQuestionChoiceModel->sort_order = $sort_order[0]->sort_order;
            $newQuestionChoiceModel->save();
        });

        $returnQuestionChoice = Question::find($questionId)
            ->choices()
            ->find($choiceId);

        return response()->json([
            'choice' => $returnQuestionChoice
        ], Response::HTTP_OK);
    }

	public function createQuestionChoice(Request $request, $questionId) {

		$validator = Validator::make(array_merge($request->all(), [
				'question_id' => $questionId]), [
				'question_id' => 'required|string|min:36|exists:question,id',
				'locale_id' => 'required|string|min:36|exists:locale,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionChoiceId = Uuid::uuid4();
		$choiceId = Uuid::uuid4();
		$translationId = Uuid::uuid4();
		$translationTextId = Uuid::uuid4();

		$newQuestionChoiceModel = new QuestionChoice;

		DB::transaction(function() use($request, $questionChoiceId, $choiceId, $translationId, $translationTextId, $questionId, $newQuestionChoiceModel) {

			$newTranslationModel = new Translation;
			$newTranslationModel->id = $translationId;
			$newTranslationModel->save();

			$newTranslationTextModel = new TranslationText;
			$newTranslationTextModel->id = $translationTextId;
			$newTranslationTextModel->translation_id = $translationId;
			$newTranslationTextModel->locale_id = $request->input('locale_id');
			$newTranslationTextModel->translated_text = $request->input('translated_text');
			$newTranslationTextModel->save();

			$newChoiceModel = new Choice;
			$newChoiceModel->id = $choiceId;
			$newChoiceModel->choice_translation_id = $translationId;
			$newChoiceModel->val = $request->input('val');
			$newChoiceModel->save();

			// Get the next available sort_order for the question, this should avoid race conditions as it is within the same transaction
			$sort_order = DB::select('select (ifnull((max(sort_order) + 1), 1)) as sort_order from question_choice where question_id = ?', [$questionId]);
			$newQuestionChoiceModel->id = $questionChoiceId;
			$newQuestionChoiceModel->question_id = $questionId;
			$newQuestionChoiceModel->choice_id = $choiceId;

			//$newQuestionChoiceModel->sort_order = $request->input('sort_order');
			//$newQuestionChoiceModel->sort_order = DB::raw('select ifnull((max(sort_order) + 1), 1) from question_choice where question_id = $questionId');
			$newQuestionChoiceModel->sort_order = $sort_order[0]->sort_order;
			$newQuestionChoiceModel->save();
			//DB::insert("insert into question_choice qc1 (id, question_id, choice_id, sort_order) values ('?', '?', '?', (select ifnull((max(qc2.sort_order) + 1), 1) from question_choice qc2 where qc2.question_id = '?')", [$questionChoiceId, $questionId, $choiceId, $questionId]);

			$newQuestionChoiceModel->translated_text = $request->input('translated_text');
			$newQuestionChoiceModel->val = $request->input('val');

		});

		/*
		$newQuestionChoiceModel = DB::select("select qc.id, qc.question_id, qc.choice_id, qc.sort_order,
                                                               c.val, c.choice_translation_id,
                                                               (select translated_text from translation_text where locale_id = '?' and translation_id = c.choice_translation_id) as translated_text
                                                               from question_choice qc where qc.id = '?' left join choice c on qc.choice_id = c.id",[$request->input('locale_id'), $questionChoiceId]);
		*/

		return response()->json([
				'questionChoice' => $newQuestionChoiceModel
		], Response::HTTP_OK);
	}

    public function removeChoice($questionId, $choiceId) {
        $validator = Validator::make([
            'question_id' => $questionId,
            'choice_id' => $choiceId], [
            'question_id' => 'required|string|min:36|exists:question,id',
            'choice_id' => 'required|string|min:36|exists:choice,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $questionChoice = QuestionChoice::where('question_id', $questionId)
            ->where('choice_id', $choiceId)
            ->firstOrFail();

        //TODO: delete choice when orphaned

        $questionChoice->delete();

        return response()->json(
            [],
            Response::HTTP_OK
        );
    }

	public function removeQuestionChoice(Request $request, $id) {

		$validator = Validator::make(
				['id' => $id],
				['id' => 'required|string|min:36|exists:question_choice,id']
		);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		};

		$questionChoiceModel = QuestionChoice::find($id);

		if ($questionChoiceModel === null) {
			return response()->json([
					'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		};

		DB::transaction(function() use($request, $questionChoiceModel) {
			$choiceModel = Choice::find($questionChoiceModel->choice_id);
			$translationTextModel = TranslationText::where('translation_id', $choiceModel->choice_translation_id);
			$translationModel = Translation::find($choiceModel->choice_translation_id);

			$translationTextModel->delete();
			$translationModel->delete();
			$choiceModel->delete();
			$questionChoiceModel->delete();
		});

		return response()->json([

		]);
	}

	public function getQuestionChoice(Request $request, $id) {

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

		$questionGroupModel = QuestionGroup::find($id);

		if ($questionGroupModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'questionGroup' => $questionGroupModel
		], Response::HTTP_OK);
	}

	public function getAllQuestionChoices(Request $request, $formId, $localeId) {

		$validator = Validator::make(array_merge($request->all(),[
				'formId' => $formId,
				'localeId' => $localeId
		]), [
				'formId' => 'required|string|min:36|exists:form,id',
				'localeId' => 'required|string|min:36|exists:locale,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionChoiceModel = QuestionChoice::select('question_choice.id', 'question_choice.sort_order', 'question_choice.question_id', 'tt.translated_text AS text', 'c.val')
				->join('choice AS c', 'c.id', '=', 'question_choice.choice_id')
				->join('translation_text AS tt', 'tt.translation_id', '=', 'c.choice_translation_id')
				->join('question AS q', 'q.id', '=', 'question_choice.question_id')
				->join('section_question_group AS sqg', 'sqg.question_group_id', '=', 'q.question_group_id')
				->join('form_section AS fs', 'fs.section_id', '=', 'sqg.section_id')
				->where('fs.form_id', $formId)
				->where('tt.locale_id', $localeId)
				->get();

		return response()->json(
			['questionChoices' => $questionChoiceModel],
			Response::HTTP_OK
		);
	}

	public function updateQuestionChoices(Request $request, $question_id) {
        $validator = Validator::make(array_merge($request->all(), ['question_id' => $question_id]),
            [
                'question_id' => 'required|string|min:36|exists:question,id',
                'locale_id' => 'required|string|min:36|exists:locale,id',
                // TODO: individual choice validation
                'choices' => 'required|array'
            ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $choices = $request->input('choices');
        $locale_id = $request->input('locale_id');

        $returnChoices = array();

        foreach ($choices as $choice) {
            $questionChoiceModel = QuestionChoice::find($choice['id']);

            if ($questionChoiceModel === null) {
                // Create a new choice
                $newChoice = $this->createChoice($choice['val'], $choice['text'], $choice['sort_order'], $locale_id, $question_id);
                $returnChoices[] = $newChoice;
            } else {
                // Update sort order
                $questionChoiceModel->sort_order = $choice['sort_order'];
                $questionChoiceModel->save();

                // Update an existing choice
                $choiceId = $questionChoiceModel->choice_id;
                $val = $choice['val'];
                $text = $choice['text'];
                if ($this->updateChoice($choiceId, $val, $text)) {
                    if (array_key_exists('deleted', $choice) && $choice['deleted']) {
                        $questionChoiceModel->delete();
                    } else {
                        $returnChoices[] = $choice;
                    }
                } else {
                    return response()->json([
                        'msg' => 'URL resource not found'
                    ], Response::HTTP_NOT_FOUND);
                }
            }
        }

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK],
            'choices' => $returnChoices
        ], Response::HTTP_OK);
    }

    public function updateChoice($choiceId, $val, $text) {
        $choiceModel = Choice::find($choiceId);

        if ($choiceModel === null) {
            return false;
        }

        if ($val != null) {
            $choiceModel->val = $val;
            $choiceModel->save();
        }

        if ($text != null) {
            $translationModel = TranslationText::where('translation_id', $choiceModel->choice_translation_id)->first();
            $translationModel->translated_text = $text;
            $translationModel->save();
        }

        return true;
    }

    public function createChoice($val, $text, $sortOrder, $localeId, $questionId) {
        $questionChoiceId = Uuid::uuid4();
        $choiceId = Uuid::uuid4();
        $translationId = Uuid::uuid4();
        $translationTextId = Uuid::uuid4();

        $newQuestionChoiceModel = new QuestionChoice;

        DB::transaction(function() use($val, $text, $sortOrder, $localeId, $questionId, $questionChoiceId, $choiceId, $translationId, $translationTextId, $newQuestionChoiceModel) {

            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            $newTranslationTextModel = new TranslationText;
            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $localeId;
            $newTranslationTextModel->translated_text = $text;
            $newTranslationTextModel->save();

            $newChoiceModel = new Choice;
            $newChoiceModel->id = $choiceId;
            $newChoiceModel->choice_translation_id = $translationId;
            $newChoiceModel->val = $val;
            $newChoiceModel->save();

            $newQuestionChoiceModel->id = $questionChoiceId;
            $newQuestionChoiceModel->question_id = $questionId;
            $newQuestionChoiceModel->choice_id = $choiceId;
            $newQuestionChoiceModel->sort_order = $sortOrder;
            $newQuestionChoiceModel->save();

            $newQuestionChoiceModel->text = $text;
            $newQuestionChoiceModel->val = $val;
        });

        return $newQuestionChoiceModel;
    }

	public function updateQuestionChoice(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
				'id' => $id
		]), [
				'id' => 'required|string|min:36|exists:question_choice,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionChoiceModel = QuestionChoice::find($id);

		if ($questionChoiceModel === null) {
			return response()->json([
					'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$choiceModel = Choice::find($questionChoiceModel->choice_id);

		if ($choiceModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		if ($request->input('val') != null) {
			$choiceModel->val = $request->input('val');
			$choiceModel->save();
		}

		if ($request->input('translated_text') != null) {
			$translationModel = TranslationText::where('translation_id', $choiceModel->choice_translation_id)->first();

			$translationModel->translated_text = $request->input('translated_text');
			$translationModel->save();
		}

		return response()->json([
				'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}
}

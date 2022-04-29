<?php

$router->group(['prefix' => 'builder', 'middleware' => ['token', 'requires:EDIT_FORM']], function () use ($router) {

  //* Form Controller Routes *//
  $router->post('study/form/{formId}/section/import',                                 'FormController@importSection');

  //* Translation Controller Routes *//
  $router->get('translation/{translation_id}/text/{text_id}',                         'TranslationTextController@getTranslationText');
  $router->delete('translation/{translation_id}',                                     'TranslationController@removeTranslation');

  $router->delete('translation/{translation_id}/text/{text_id}',                      'TranslationTextController@removeTranslationText');
  $router->post('translation/{translation_id}/text/{text_id}',                        'TranslationTextController@updateTranslationText');
  $router->get('translation/{translation_id}/text',                                   'TranslationTextController@getAllTranslationText');
  $router->put('translation',                                                         'TranslationController@createTranslation');
  $router->put('translation/{translation_id}/text',                                   'TranslationTextController@createTranslationText');

  //* Question Group Controller Routes *//
  $router->get('group/{group_id}',                                               'QuestionGroupController@getQuestionGroup');
  $router->delete('group/{group_id}',                                            'QuestionGroupController@removeQuestionGroup');
  $router->post('section/{section_id}/group',                                    'QuestionGroupController@createQuestionGroup');
  $router->put('group/{group_id}',                                               'QuestionGroupController@updateQuestionGroup');
  $router->put('section-question-group/{id}',                                    'SectionQuestionGroupController@updateSectionQuestionGroup');

  //* Section Controller Routes *//
  $router->get('section/{section_id}',                                           'SectionController@getSection');
  $router->delete('section/{section_id}',                                        'SectionController@removeSection');
  $router->put('section/{section_id}',                                           'SectionController@updateSection');
  $router->get('{form_id}/section/locale/{locale_id}',                           'SectionController@getAllSections');
  $router->post('{form_id}/section',                                             'SectionController@createSection');
  $router->patch('sections',                                                     'SectionController@updateSections');


  //* Form Section Controller Routes *//
  $router->put('form_section/{form_section_id}',                                     'FormSectionController@updateFormSection');

  //* Question Condition Controller Routes *//
  $router->put('section/group/question/condition/logic',                                                  'ConditionController@editConditionLogic');
  $router->put('section/group/question/condition/scope',                                                  'ConditionController@editConditionScope');
  $router->post('condition-tag',                                                      'ConditionController@createCondition');
  $router->get('section/group/question/condition/tag',                                                      'ConditionController@getAllConditions');
  $router->post('question/{question_id}/assign_condition_tag',                                                  'QuestionController@createAssignConditionTag');
  $router->put('question/{question_id}/assign_condition_tag',                                                'QuestionController@updateAssignConditionTag');
  $router->delete('condition/{id}',                                              'ConditionController@deleteAssignConditionTag');

  //* Skip Controller Routes *//
  $router->post('group/{group_id}/skip/',                                       'SkipController@createQuestionGroupSkip');
  $router->put('skip/{id}',                                                     'SkipController@updateSkip');
  $router->delete('group/skip/{id}',                                                  'SkipController@deleteQuestionGroupSkip');
  $router->post('{form_id}/skip',                                               'SkipController@createFormSkip');
  $router->delete('{form_id}/skip/{skip_id}',                                   'SkipController@deleteFormSkip');

  //* Question Controller Routes *//
  $router->post('group/{group_id}/question/',                                     'QuestionController@createQuestion');
  $router->put('group/{group_id}/question/{question_id}',                         'QuestionController@moveQuestion');
  $router->delete('question/{question_id}',                                       'QuestionController@removeQuestion');
  // $router->get('question/{question_id}',                                       'QuestionController@getQuestion');
  $router->put('question/{question_id}',                                          'QuestionController@updateQuestion');
  $router->patch('section/group/questions',                                       'QuestionController@updateQuestions');
  $router->patch('section/group/question/choices',                                'QuestionController@updateChoices');

  //* Question Type Controller Routers *//
  $router->get('question/types',                                                       'QuestionTypeController@getAllQuestionTypes');

  //* Question Choice Controller Routes *//
  $router->post('question/{question_id}/choice',                    'QuestionChoiceController@createNewQuestionChoice');
  $router->delete('choice/{question_choice_id}',                    'QuestionChoiceController@removeQuestionChoice');
  $router->delete('question/{question_id}/choice/{choice_id}',      'QuestionChoiceController@removeChoice');
  $router->get('question/choice/{choice_id}',                       'QuestionChoiceController@getQuestionChoice');
  $router->put('choice/{choice_id}',                                'QuestionChoiceController@updateQuestionChoice');
  $router->put('question/{question_id}/choices',                    'QuestionChoiceController@updateQuestionChoices');
  $router->put('question/choice/{question_choice_id}',              'QuestionChoiceController@updateQuestionChoice2');

  //* Question Param Controller Routes *//
  $router->get('parameter/types',                                                'QuestionParamController@getParameterTypes');
  $router->post('question/{question_id}/parameter',                'QuestionParamController@createOrUpdateParameter');
  $router->delete('parameter/{parameter_id}',                                         'QuestionParamController@deleteQuestionParameter');
});

<?php

//***************************//
//* Token Controller Routes *//
//***************************//

$app->post(
	'token',
	'TokenController@createToken'
);

//**************************//
//* Sync Controller Routes *//
//**************************//

$app->get(
	'heartbeat',
	'SyncController@heartbeat'
);

$app->put(
	'device/{device_id}/sync/image',
	'SyncController@uploadImages'
);

$app->put(
	'device/{device_id}/sync',
	'SyncController@upload'
);

$app->post(
	'device/{device_id}/sync',
	'SyncController@download'
);

$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => 'token'], function($app){

	//**************************//
	//* Form Controller Routes *//
	//**************************//

	$app->get(
		'form/{id}',
		'FormController@getForm'
	);

	$app->delete(
		'form/{id}',
		'FormController@removeForm'
	);

	$app->post(
		'form/{id}',
		'FormController@updateForm'
	);

	$app->get(
		'form',
		'FormController@getAllForms'
	);

	$app->get(
		'study/{studyId}/form/locale/{localeId}',
		'FormController@getAllStudyForms'
	);

	$app->put(
		'form',
		'FormController@createForm'
	);

	$app->get(
		'study/{studyId}/form/{formId}/master/{formMasterId}/edit',
		'FormController@editFormPrep'
	);

	//***************************//
	//* Study Controller Routes *//
	//***************************//

	$app->get(
		'study/{id}',
		'StudyController@getStudy'
	);

	$app->delete(
		'study/{id}',
		'StudyController@removeStudy'
	);

	$app->post(
		'study/{id}',
		'StudyController@updateStudy'
	);

	$app->get(
		'study',
		'StudyController@getAllStudies'
	);

	$app->put(
		'study',
		'StudyController@createStudy'
	);

	//**************************//
	//* User Controller Routes *//
	//**************************//

	$app->get(
		'user/{id}',
		'UserController@getUser'
	);

	$app->delete(
		'user/{id}',
		'UserController@removeUser'
	);

	$app->post(
		'user/{id}',
		'UserController@updateUser'
	);

	$app->get(
		'user',
		'UserController@getAllUsers'
	);

	$app->put(
		'user',
		'UserController@createUser'
	);

	//****************************//
	//* Locale Controller Routes *//
	//****************************//

	$app->get(
		'locale/{id}',
		'LocaleController@getLocale'
	);

	$app->delete(
		'locale/{id}',
		'LocaleController@removeLocale'
	);

	$app->post(
		'locale/{id}',
		'LocaleController@updateLocale'
	);

	$app->get(
		'locale',
		'LocaleController@getAllLocales'
	);

	$app->put(
		'locale',
		'LocaleController@createLocale'
	);

	//****************************//
	//* Device Controller Routes *//
	//****************************//

	$app->get(
		'device/{id}',
		'DeviceController@getDevice'
	);

	$app->delete(
		'device/{id}',
		'DeviceController@removeDevice'
	);

	$app->post(
		'device/{id}',
		'DeviceController@updateDevice'
	);

	$app->get(
		'device',
		'DeviceController@getAllDevices'
	);

	$app->put(
		'device',
		'DeviceController@createDevice'
	);

	//**************************************//
	//* Translation Controller Routes *//
	//**************************************//

	$app->get(
			'translation/{translation_id}/text/{text_id}',
			'TranslationTextController@getTranslationText'
	);

	$app->delete(
			'translation/{translation_id}',
			'TranslationController@removeTranslation'
	);

	$app->delete(
			'translation/{translation_id}/text/{text_id}',
			'TranslationTextController@removeTranslationText'
	);

	$app->post(
			'translation/{translation_id}/text/{text_id}',
			'TranslationTextController@updateTranslationText'
	);

	$app->get(
			'translation/{translation_id}/text',
			'TranslationTextController@getAllTranslationText'
	);

	$app->put(
			'translation',
			'TranslationController@createTranslation'
	);

	$app->put(
			'translation/{translation_id}/text',
			'TranslationTextController@createTranslationText'
	);

	//************************************//
	//* Question Group Controller Routes *//
	//************************************//

	$app->get(
			'form/section/group/{group_id}/question/',
			'QuestionGroupController@getQuestionGroup'
	);

	$app->delete(
			'form/section/group/{group_id}/question/',
			'QuestionGroupController@removeQuestionGroup'
	);

	$app->get(
			'form/{form_id}/section/group/locale/{locale_id}',
			'QuestionGroupController@getAllQuestionGroups'
	);

	$app->put(
			'form/section/{section_id}/group/question',
			'QuestionGroupController@createQuestionGroup'
	);

	$app->post(
			'form/section/group/{group_id}/question/',
			'QuestionGroupController@updateQuestionGroup'
	);

	//*****************************//
	//* Section Controller Routes *//
	//*****************************//

	$app->get(
			'form/section/{section_id}',
			'SectionController@getSection'
	);

	$app->delete(
			'form/section/{section_id}',
			'SectionController@removeSection'
	);

	$app->post(
			'form/section/{section_id}',
			'SectionController@updateSection'
	);

	$app->get(
			'form/{form_id}/section/locale/{locale_id}',
			'SectionController@getAllSections'
	);

	$app->put(
			'form/{form_id}/section',
			'SectionController@createSection'
	);

	//****************************************//
	//* Question Condition Controller Routes *//
	//****************************************//

	$app->put(
		'form/section/group/question/condition/tag',
		'ConditionController@createCondition'
	);

	$app->get(
		'form/section/group/question/condition/tag',
		'ConditionController@getAllConditions'
	);

	$app->get(
		'form/section/group/question/condition/tag/unique',
		'ConditionController@getAllUniqueConditions'
	);

	//**************************//
	//* Skip Controller Routes *//
	//**************************//

	$app->put(
		'form/section/group/skip/',
		'SkipController@createQuestionGroupSkip'
	);

	$app->get(
		'form/section/group/skip/',
		'SkipController@getAllQuestionGroupSkips'
	);

	//******************************//
	//* Question Controller Routes *//
	//******************************//

	$app->put(
			'form/section/group/{group_id}/question/',
			'QuestionController@createQuestion'
	);

	$app->delete(
			'form/section/group/question/{question_id}',
			'QuestionController@removeQuestion'
	);

	$app->get(
			'form/section/group/question/{question_id}',
			'QuestionController@getQuestion'
	);

	$app->get(
			'form/{form_id}/section/group/question/locale/{locale_id}',
			'QuestionController@getAllQuestions'
	);

	$app->post(
			'form/section/group/question/{question_id}',
			'QuestionController@updateQuestion'
	);

	//************************************//
	//* Question Type Controller Routers *//
	//************************************//

	$app->put(
			'question/type',
			'QuestionTypeController@createQuestionType'
	);

	$app->delete(
			'question/type/{question_type_id}',
			'QuestionTypeController@removeQuestionType'
	);

	$app->get(
			'question/type/{question_type_id}',
			'QuestionTypeController@getQuestionType'
	);

	$app->get(
			'question/type',
			'QuestionTypeController@getAllQuestionTypes'
	);

	$app->post(
			'question/type/{question_type_id}',
			'QuestionTypeController@updateQuestionType'
	);

	//*************************************//
	//* Question Choice Controller Routes *//
	//*************************************//

	$app->put(
			'form/section/group/question/{question_id}/choice',
			'QuestionChoiceController@createQuestionChoice'
	);

	$app->delete(
			'form/section/group/question/choice/{question_choice_id}',
			'QuestionChoiceController@removeQuestionChoice'
	);

	$app->get(
			'form/section/group/question/choice/{choice_id}',
			'QuestionChoiceController@getQuestionChoice'
	);

	$app->get(
			'form/{form_id}/section/group/question/choice/locale/{locale_id}',
			'QuestionChoiceController@getAllQuestionChoices'
	);

	$app->post(
			'form/section/group/question/choice/{choice_id}',
			'QuestionChoiceController@updateQuestionChoice'
	);

	//*************************//
	//* Geo Controller Routes *//
	//*************************//

	$app->put(
		'geo/id/locale/{locale_id}',
		'GeoController@createGeo'
	);

	$app->delete(
		'gep/id/{geo_id}',
		'GeoController@removeGeo'
	);

	$app->get(
		'geo/id/locale/{locale_id}',
		'GeoController@getAllGeos'
	);

	$app->get(
		'geo/id/{geo_id}',
		'GeoController@getGeo'
	);

	$app->post(
		'geo/id/{geo_id}',
		'GeoController@updateGeo'
	);

	//******************************//
	//* Geo Type Controller Routes *//
	//******************************//

	$app->put(
		'geo/type',
		'GeoTypeController@createGeoType'
	);

	$app->delete(
		'gep/type/{geo_type_id}',
		'GeoTypeController@removeGeoType'
	);

	$app->get(
		'geo/type/{geo_type_id}',
		'GeoTypeController@getGeoType'
	);

	$app->get(
		'geo/type',
		'GeoTypeController@getAllGeoTypes'
	);

	$app->get(
		'geo/type/{geo_type_id}/parent',
		'GeoTypeController@getAllEligibleGeoTypesOfParentGeo'
	);

	$app->post(
		'geo/type/{geo_type_id}',
		'GeoTypeController@updateGeoType'
	);

	//************************************//
	//* Question Param Controller Routes *//
	//************************************//

	$app->post(
			'form/section/group/question/{question_id}/type/numeric',
			'QuestionParamController@updateQuestionNumeric'
	);

	$app->post(
			'form/section/group/question/{question_id}/type/multiple',
			'QuestionController@updateQuestionTypeMultiple'
	);

	$app->post(
			'form/section/group/question/{question_id}/type/datetime',
			'QuestionParamController@updateQuestionDateTime'
	);
});

<?php

namespace app\Services;

use App\Models\ConditionTag;
use App\Models\Config;
use App\Models\Locale;
use App\Models\Study;
use App\Models\StudyLocale;
use App\Models\User;
use App\Models\UserConfirmation;
use App\Models\UserStudy;
use App\Services\RespondentService;
use App\Services\GeoService;
use App\Services\FormService;
use App\Services\ConditionTagService;
use Log;
use Ramsey\Uuid\Uuid;

class DemoService {

    public function makeDemoUser (UserConfirmation $confirmation, $role) {
      $user = new User;
      $user->id = Uuid::uuid4();
      $user->password = $confirmation->password;
      $user->username = $confirmation->username;
      $user->email = $confirmation->email;
      $user->name = $confirmation->name;
      $user->role_id = $role;
      $user->save();

      $study = new Study;
      $study->id = Uuid::uuid4();
      $study->name = ucwords($user->username . ' Study');
      $study->default_locale_id = Locale::where('language_name', 'like', 'english')->first()->id;
      $study->save();

      Log::debug("Created study: $study->name");

      $studyLocale = new StudyLocale;
      $studyLocale->id = Uuid::uuid4();
      $studyLocale->locale_id = $study->default_locale_id;
      $studyLocale->study_id = $study->id;
      $studyLocale->save();

      $userStudy = new UserStudy;
      $userStudy->id = Uuid::uuid4();
      $userStudy->study_id = $study->id;
      $userStudy->user_id = $user->id;
      $userStudy->save();

      // Import demo respondents and assign them to this study
      $numRespondents = count(RespondentService::importRespondentsFromFile(resource_path('demo/respondents.csv'), $study->id, true));
      Log::debug("Added $numRespondents respondents to study: $study->name");
      $numPhotos = RespondentService::importRespondentPhotos(resource_path('demo/respondent_photos.zip'), $study->id);
      Log::debug("Added $numPhotos photos to study: $study->name");

      $geoService = new GeoService();

      $numGeos = count($geoService->importGeosFromFile(resource_path('demo/states.csv'), $study->id));
      $numGeos += count($geoService->importGeosFromFile(resource_path('demo/cities.csv'), $study->id));
      Log::debug("Added $numGeos geos to study: $study->name");

      $numGeoPhotos = $geoService->importGeoPhotos(resource_path('demo/state_capital_photos.zip'), $study->id);
      Log::debug("Added $numGeoPhotos geo photos to study: $study->name");

      $conditionTagService = new ConditionTagService();
      $numRespondentConditionTags = count($conditionTagService->importRespondentConditionTagsFromFile(resource_path('demo/respondent_condition_tags.csv'), $study->id));
      Log::debug("Added $numRespondentConditionTags respondent condition tags to study: $study->name");

      $respondentService = new RespondentService();
      $numRespondentGeos = count($respondentService->importRespondentGeosFromFile(resource_path('demo/respondent_locations.csv'), $study->id));
      Log::debug("Added $numRespondentGeos respondent geos to study: $study->name");

      // Load all of the forms
      $importedForm = FormService::importFormAndAddToStudy(resource_path('demo/forms/example-question-types.json'), 'Example Question Types', $study->id, 0);
      $importedForm->is_published = true;
      $importedForm->save();
    }
}

<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Services\GeoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller {

  function getCounts (String $studyId) {
    $validator = Validator::make([
      'study' => $studyId,
    ], [
      'study' => 'string|exists:study,id'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], Response::HTTP_BAD_REQUEST);
    }

    $res = [];

    $res['geos'] = DB::table('geo')->
      whereIn('geo_type_id', function ($q) use ($studyId) {
        return $q->
          select('id')->
          from('geo_type')->
          where('study_id', $studyId);
      })->
      whereNull('deleted_at')->
      count();

    $res['surveys'] = DB::table('survey')->
      where('study_id', $studyId)->
      whereNull('deleted_at')->
      count();
    $res['forms'] = DB::table('form')->
      whereIn('id', function ($q) use ($studyId) {
        return $q->
          select('form_master_id')->
          from('study_form')->
          where('study_id', $studyId);
      })->
      where('is_published', true)->
      whereNull('deleted_at')->
      count();
    $res['respondents'] = DB::table('respondent')->
      whereIn('id', function ($q) use ($studyId) {
        return $q->
          select('respondent_id')->
          from('study_respondent')->
          where('study_id', $studyId);
      })->
      whereNull('deleted_at')->
      count();
    $res['users'] = DB::table('user')->
      whereIn('id', function ($q) use ($studyId) {
        return $q->
          select('user_id')->
          from('user_study')->
          where('study_id', $studyId);
      })->
      whereNull('deleted_at')->
      count();
    
    $res['photos'] = DB::table('respondent_photo')->
      whereIn('respondent_id', function ($q) use ($studyId) {
        return $q->
          select('respondent_id')->
          from('study_respondent')->
          where('study_id', $studyId);
      })->
      whereNull('deleted_at')->
      count();

    $res['photos'] += DB::table('geo_photo')->
      whereIn('geo_id', function ($q) use ($studyId) {
        return $q->
          select('id')->
          from('geo')->
          whereIn('geo_type_id', function ($q) use ($studyId) {
            return $q->
              select('id')->
              from('geo_type')->
              where('study_id', $studyId);
          });
      })->
      whereNull('deleted_at')->
      count();

    return $res;
  }

  function getSurveys (Request $req, string $studyId) {
    Validator::make(array_merge($req->all(), [
      'study' => $studyId, 
    ]), [
      'study' => 'required|string|exists:study,id',
      'min' => 'required|string',
      'max' => 'string',
    ])->validate();

    $max = $req->get('max') ? Carbon::parse($req->get('max')) : Carbon::today();
    $min = $req->get('min');
    $max = $max->format('Y-m-d');

    $surveys = DB::table('survey')->
      selectRaw('date(created_at) date, count(*) n')->
      where('created_at', '>=', $min)->
      where('created_at', '<=', $max)->
      where('study_id', $studyId)->
      whereNull('deleted_at')->
      orderBy('created_at')->
      groupBy(DB::raw('date(created_at)'))->
      get();

    return [
      'labels' => $surveys->map(function ($u) { return $u->date; }),
      'data' => $surveys->map(function ($u) { return $u->n; }),
    ];
  }

  function getRespondents (Request $req, string $studyId) {
    Validator::make(array_merge($req->all(), [
      'study' => $studyId, 
    ]), [
      'study' => 'required|string|exists:study,id',
      'min' => 'required|string',
      'max' => 'string'
    ])->validate();

    $max = $req->get('max') ? Carbon::parse($req->get('max')) : Carbon::today();
    $min = $req->get('min');
    $max = $max->format('Y-m-d');

    $respondents = DB::table('study_respondent')->
      selectRaw('date(created_at) date, count(*) n')->
      where('created_at', '>=', $min)->
      where('created_at', '<=', $max)->
      where('study_id', $studyId)->
      whereNull('deleted_at')->
      orderBy('created_at')->
      groupBy(DB::raw('date(created_at)'))->
      get();

      return [
        'labels' => $respondents->map(function ($u) { return $u->date; }),
        'data' => $respondents->map(function ($u) { return $u->n; }),
      ];
  }

  function getUsers (Request $req, string $studyId) {
    Validator::make(array_merge($req->all(), [
      'study' => $studyId, 
    ]), [
      'study' => 'required|string|exists:study,id',
      'min' => 'required|string',
      'max' => 'string'
    ])->validate();

    $max = $req->get('max') ? Carbon::parse($req->get('max')) : Carbon::today();
    $min = $req->get('min');
    $max = $max->format('Y-m-d');

    $users = DB::table('user_study')->
      selectRaw('date(created_at) date, count(*) n')->
      where('created_at', '>=', $min)->
      where('created_at', '<=', $max)->
      where('study_id', $studyId)->
      whereNull('deleted_at')->
      orderBy('created_at')->
      groupBy(DB::raw('date(created_at)'))->
      get();

    return [
      'labels' => $users->map(function ($u) { return $u->date; }),
      'data' => $users->map(function ($u) { return $u->n; }),
    ];
  }

  function getGeos (Request $req, string $studyId) {
    Validator::make(array_merge($req->all(), [
      'study' => $studyId, 
    ]), [
      'study' => 'required|string|exists:study,id',
      'min' => 'required|string',
      'max' => 'string'
    ])->validate();

    $max = $req->get('max') ? Carbon::parse($req->get('max')) : Carbon::today();
    $min = $req->get('min');
    $max = $max->format('Y-m-d');

    $users = DB::table('geo')->
      selectRaw('date(created_at) date, count(*) n')->
      where('created_at', '>=', $min)->
      where('created_at', '<=', $max)->
      whereIn('geo_type_id', function ($q) use ($studyId) {
        return $q->select('id')->
          from('geo_type')->
          where('study_id', $studyId);
      })->
      whereNull('deleted_at')->
      orderBy('created_at')->
      groupBy(DB::raw('date(created_at)'))->
      get();

    return [
      'labels' => $users->map(function ($u) { return $u->date; }),
      'data' => $users->map(function ($u) { return $u->n; }),
    ];
  }

  function getForms (Request $req, string $studyId) {
    Validator::make(array_merge($req->all(), [
      'study' => $studyId,
    ]), [
      'study' => 'required|string|exists:study,id',
      'forms' => 'string',
      'conditionTags' => 'array',
      'respondents' => 'array',
      'geos' => 'array',
      'users' => 'array',
      'min' => 'required|string',
      'max' => 'string',
      'onlyPublished' => 'boolean'
    ])->validate();

    $forms = [];

    if ($req->has('forms')) {
      $formIds = explode(",", $req->get('forms'));
      $forms = Form::whereIn('id', $formIds)->
        with('nameTranslation', 'studyForm')->
        whereNull('deleted_at');
    } else {
      $forms = Form::whereIn('form_master_id', function ($q) use ($studyId) {
          $q->
            select('form_master_id')->
            from('study_form')->
            where('study_id', $studyId)->
            whereNull('deleted_at');
        });
    }

    if ($req->get('onlyPublished')) {
      $forms = $forms->where('is_published', true);
    }

    $forms = $forms->get();

    $max = $req->get('max') ? Carbon::parse($req->get('max')) : Carbon::today();
    $min = $req->get('min');
    $max = $max->format('Y-m-d');


    $formIds = $forms->map(function ($f) { return $f->id; });
    $surveys = DB::table('survey')->
      selectRaw('(select form_master_id from form where id = form_id) fmid, date(created_at) date, count(*) n')->
      where('created_at', '>=', $min)->
      where('created_at', '<=', $max)->
      where('study_id', $studyId)->
      whereNull('deleted_at')->
      orderBy('created_at')->
      groupBy(DB::raw('fmid, date(created_at)'));
    
    // Limit to all versions of the given form
    if (count($forms)) {
      $surveys = $surveys->whereIn('form_id', function ($q) use ($formIds) {
        return $q->
          select('id')->
          from('form')->
          whereIn('id', $formIds);
      });
    }

    // Limit returned surveys to only specified users
    if ($req->has('users')) {
      $users = $req->get('users');
      $surveys = $surveys->whereIn('id', function ($q) use ($users) {
        return $q->
          select('survey_id')->
          from('interview')->
          whereIn('user_id', $users)->
          whereNull('deleted_at');
      });
    }

    // Filter to surveys from respondents with these condition tags assigned
    if ($req->has('conditionTags')) {
      $tags = $req->get('conditionTags');
      foreach ($tags as $tag) {
        $surveys = $surveys->whereIn('respondent_id', function ($q) use ($tag) {
          $q->
            select('respondent_id')->
            from('respondent_condition_tag')->
            whereNull('deleted_at')->
            whereIn('condition_tag_id', function ($q) use ($tag) {
              $q->
                select('id')->
                from('condition_tag')->
                whereNull('deleted_at')->
                where('name', $tag);
            });
        });
      }
    }

    if ($req->has('respondents')) {
      $surveys = $surveys->whereIn('respondent_id', $req->get('respondents'));
    }

    if ($req->has('geos')) {
      $geoIds = GeoService::getNestedGeoIds($req->get('geos'));
      $surveys = $surveys->whereIn('respondent_id', function ($q) use ($geoIds) {
        $q->
          select('respondent_id')->
          from('respondent_geo')->
          whereIn('geo_id', $geoIds);
      });
    }
    
    $surveys = $surveys->get();
    $res = [];

    // Convert data into labels and counts
    foreach ($surveys as $survey) {
      if (!isset($res[$survey->fmid])) {
        $res[$survey->fmid] = [
          'form' => Form::with('nameTranslation', 'studyForm')->find($survey->fmid),
          'data' => [
            'labels' => [],
            'data' => [],
          ],
        ];
      }
      // append data
      $res[$survey->fmid]['data']['data'][] = $survey->n;
      $res[$survey->fmid]['data']['labels'][] = $survey->date;
    }

    return $res;
  }

}
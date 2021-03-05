<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Validator;

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
      'max' => 'string'
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

}
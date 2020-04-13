<?php namespace App\Http\Controllers;

use App\Models\Roster;
use DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Validator;

class RosterController extends Controller {

    /**
     * Get an array of rosters by a comma delimited list of ids
     * @param {string} $ids - the ids separated by commas
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function getRostersById ($ids) {
	    $rosterIds = explode(',', $ids);
	    $validator = Validator::make([
	        'rosterIds' => $rosterIds
        ], [
            'rosterIds' => 'required|array|exists:roster,id'
        ]);

	    if ($validator->fails()) {
	        return response()->json([
	            'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $rosters = Roster::whereIn('id', $rosterIds)->get();

	    return response()->json([
	        'rosters' => $rosters
        ], Response::HTTP_OK);
    }

    /**
     * Create multiple roster rows with an array of values.
     * @param Request $request - should include an array of roster values
     * @return \Symfony\Component\HttpFoundation\Response - A JSON object with a rosters array defined
     */
    public function createRosterRows (Request $request) {
        $validator = Validator::make(array_merge($request->all()), [
            'rosters' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }
        $rosterVals = $request->get('rosters');
        $rosters = [];
        foreach ($rosterVals as $val) {
            array_push($rosters, [
                'id' => Uuid::uuid4(),
                'val' => $val
            ]);
        }

        // TODO: Handle this transaction manually? Maybe allow for rolling back.
        DB::transaction(function () use ($rosters) {
            Roster::insert($rosters);
        });

        return response()->json([
            'rosters' => $rosters
        ], Response::HTTP_OK);
    }

    /**
     * Takes an array of rosters with ids and the new values. This method will not make new rosters if the old ones don't
     * exist. All roster ids must be unique of the validation will fail.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRosterRows (Request $request) {

        $rosters = $request->get('rosters');

        $rosterIds = array_reduce($rosters, function ($ids, $roster) {
            array_push($ids, $roster['id']);
            return $ids;
        }, []);

        $validator = Validator::make([
            'rosterIds' => $rosterIds
        ], [
            'rosterIds' => 'required|array|exists:roster,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        // TODO: Maybe handle failures manually?
        DB::transaction(function () use ($rosters) {
            foreach ($rosters as $roster) {
                Roster::where('id', $roster['id'])
                    ->update([
                        'val' => $roster['val']
                    ]);
            }
        });

        return response()->json([
            'msg' => 'ok'
        ], Response::HTTP_OK);
    }


    public function getRespondentRosters (Request $request, $respondentId) {
        $uniqueId = $request->input('uniqueId');
        $validator = Validator::make([
            'respondentId' => $respondentId,
            'uniqueId' => $uniqueId
        ], [
            'respondentId' => 'required|string|min:7',
            'uniqueId' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }
        $q = DB::table('roster')
            ->innerJoin('datum', 'datum.roster_id', '=', 'roster.id')
            ->innerJoin('question_datum', 'question_datum.id', '=', 'datum.question_datum_id')
            ->innerJoin('survey', 'survey.id', '=', 'question_datum.survey_id')
            ->where('survey.respondent_id', '=', $respondentId);
        return response()->json([
            rosters => $q->get()
        ]);
    }

}
<?php namespace App\Http\Controllers;

use App\Models\Edge;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;

class EdgeController extends Controller {

    public function createEdges (Request $request) {
        $input = $request->all();
        if (!isset($input['edges']) || count($input['edges']) === 0) {
            return response()->json([
                'msg' => 'must include edges with request'
            ], Response::HTTP_BAD_REQUEST);
        }

        $edges = [];
        $respondentIdMap = array();
        // Get an array of all respondent ids
        foreach ($input['edges'] as $edge) {
            $respondentIdMap[$edge['source_respondent_id']] = $edge['source_respondent_id'];
            $respondentIdMap[$edge['target_respondent_id']] = $edge['target_respondent_id'];
            array_push($edges, [
                'id' => Uuid::uuid4(),
                'source_respondent_id' => $edge['source_respondent_id'],
                'target_respondent_id' => $edge['target_respondent_id']
            ]);
        }

        $validator = Validator::make([
            'edge_respondent_ids' => $respondentIdMap
        ], [
            'edge_respondent_ids' => 'required|exists:respondent,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        // TODO: make the edges here
        DB::transaction(function () use ($edges) {
            DB::table('edge')->insert($edges);
        }, 2);

        $edgeIds = array_reduce($edges, function ($ids, $edge) {
            array_push($ids, $edge['id']);
            return $ids;
        }, []);

        $edgesWithTarget = Edge::with('targetRespondent')->whereIn('id', $edgeIds)->get();

        return response()->json([
            'edges' => $edgesWithTarget
        ], Response::HTTP_OK);
    }

}
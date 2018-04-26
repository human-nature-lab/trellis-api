<?php namespace App\Http\Controllers;

use App\Models\Edge;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;

class EdgeController extends Controller {

    /**
     * Get a single edge by edgeId. This includes source repsondent and target respndent
     * @param $edgeId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEdge ($edgeId) {
        $validator = Validator::make([
            'edge_id' => $edgeId
        ], [
            'edge_id' => 'required|string|min:32|exists:edge,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $edge = Edge::with('sourceRespondent', 'targetRespondent')->find($edgeId);

        return response()->json([
            'edge' => $edge
        ], Response::HTTP_OK);

    }

    /**
     * Get a bunch of edges by their ids. All ids must be unique. Duplicate ids will fail validation. Ids are comma
     * separated in the url
     * @param {array} $ids - The comma separated list of edge ids
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEdgesById ($ids) {
        $edgeIds = explode(',', $ids);

        $validator = Validator::make([
            'edge_ids' => $edgeIds
        ], [
            'edge_ids' => 'required|exists:edge,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $edges = Edge::with('targetRespondent')->whereIn('id', $edgeIds)->get();

        return response()->json([
            'edges' => $edges
        ], Response::HTTP_OK);
    }

    /**
     * Create a list of edges by their source_respondent_id and target_respondent_id. It is possible to create a
     * self-loop with this method.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createEdges (Request $request) {
        $input = $request->all();
        if (!isset($input['edges']) || count($input['edges']) === 0) {
            return response()->json([
                'msg' => 'must include edges with request'
            ], Response::HTTP_BAD_REQUEST);
        }

        $edges = [];
        $respondentIdMap = array();
        // Get an array of all unique respondent ids
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

        // TODO: I guess we should perform this transaction manually and rollback if there is an error
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
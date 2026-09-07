<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\MatchingIntelligence\Actions\ConfigureThreshold;
use Liberu\Accounting\MatchingIntelligence\Actions\CreateSuggestion;
use Liberu\Accounting\MatchingIntelligence\Actions\DecideSuggestion;
use Liberu\Accounting\MatchingIntelligence\Actions\RecordFeedback;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;
use Liberu\Accounting\MatchingIntelligence\Queries\MatchingQuery;
use Liberu\Accounting\MatchingIntelligenceApi\Http\Resources\MatchingSuggestionResource;

final class MatchingIntelligenceController extends Controller
{
    public function index(Request $request, MatchingQuery $query): mixed
    {
        return MatchingSuggestionResource::collection($query->suggestions($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateSuggestion $action): MatchingSuggestionResource
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'suggestion_ref' => 'required|string|max:190', 'source_type' => 'required|string|max:100', 'source_id' => 'required|string|max:190', 'target_type' => 'required|string|max:100', 'target_id' => 'required|string|max:190', 'match_type' => 'required|string|max:80', 'confidence' => 'required|numeric|between:0,1', 'score' => 'nullable|numeric', 'automation_threshold' => 'nullable|numeric|between:0,1', 'explanation' => 'nullable|string', 'algorithm_version' => 'nullable|string|max:40', 'evidence' => 'nullable|array', 'evidence.*.kind' => 'required|string|max:50']);

        return new MatchingSuggestionResource($action->handle($data, $data['evidence'] ?? []));
    }

    public function show(MatchingSuggestion $suggestion): MatchingSuggestionResource
    {
        return new MatchingSuggestionResource($suggestion->load(['evidence', 'feedback']));
    }

    public function decide(Request $request, MatchingSuggestion $suggestion, DecideSuggestion $action): MatchingSuggestionResource
    {
        $data = $request->validate(['accept' => 'required|boolean', 'automate' => 'nullable|boolean']);

        return new MatchingSuggestionResource($action->handle($suggestion, (string) $request->user()->getAuthIdentifier(), $data['accept'], $data['automate'] ?? false));
    }

    public function feedback(Request $request, MatchingSuggestion $suggestion, RecordFeedback $action): JsonResponse
    {
        $data = $request->validate(['feedback_type' => 'required|in:correct,incorrect,partial', 'comment' => 'nullable|string']);

        return response()->json(['data' => $action->handle($suggestion, (string) $request->user()->getAuthIdentifier(), $data['feedback_type'], $data['comment'] ?? null)], 201);
    }

    public function threshold(Request $request, ConfigureThreshold $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($request->validate(['team_id' => 'nullable|integer', 'match_type' => 'required|string|max:80', 'minimum_confidence' => 'required|numeric|between:0,1', 'maximum_amount' => 'nullable|numeric', 'require_evidence' => 'nullable|boolean']))], 201);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestionsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CodingSuggestions\Actions\ApplySuggestionFeedback;
use Liberu\Accounting\CodingSuggestions\Actions\CreateCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Actions\ReviewCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Models\CodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Queries\CodingSuggestionQuery;

final class CodingSuggestionsController extends Controller
{
    public function index(CodingSuggestionQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCodingSuggestion $action): JsonResponse
    {
        $data = $request->validate(['source_ref' => ['required', 'string', 'max:160'], 'target_type' => ['required', 'string', 'max:60'], 'target_ref' => ['required', 'string', 'max:160'], 'confidence' => ['required', 'numeric', 'between:0,1'], 'explanation' => ['required', 'string']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function feedback(Request $request, string $suggestion, ApplySuggestionFeedback $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->suggestion($suggestion), $request->validate(['decision' => ['required', 'in:accepted,rejected'], 'note' => ['nullable', 'string', 'max:500']]))]);
    }

    public function review(Request $request, string $suggestion, ReviewCodingSuggestion $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->suggestion($suggestion), $request->validate(['reviewer_ref' => ['required', 'string', 'max:160']]))]);
    }

    private function suggestion(string $id): CodingSuggestion
    {
        return CodingSuggestion::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

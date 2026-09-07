<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\YearEnd\Actions\AddYearEndAdjustment;
use Liberu\Accounting\YearEnd\Actions\ArchiveYearEnd;
use Liberu\Accounting\YearEnd\Actions\CreateYearEndPeriod;
use Liberu\Accounting\YearEnd\Actions\LockYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndPeriod;
use Liberu\Accounting\YearEnd\Queries\YearEndQuery;

final class YearEndController extends Controller
{
    public function index(YearEndQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateYearEndPeriod $action): JsonResponse
    {
        $data = $request->validate(['period_ref' => ['required', 'string', 'max:160'], 'period_end' => ['required', 'date'], 'opening_balances' => ['nullable', 'array'], 'statutory_handoff' => ['nullable', 'array'], 'evidence' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function adjustment(Request $request, string $period, AddYearEndAdjustment $action): JsonResponse
    {
        $record = YearEndPeriod::query()->where('team_id', $this->teamId())->findOrFail($period);
        $data = $request->validate(['adjustment_ref' => ['required', 'string', 'max:160'], 'amount' => ['required', 'numeric', 'not_in:0'], 'description' => ['required', 'string', 'max:255'], 'journal_ref' => ['nullable', 'string', 'max:160'], 'evidence' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle($record, $data)], 201);
    }

    public function lock(Request $request, string $period, LockYearEnd $action): JsonResponse
    {
        $record = YearEndPeriod::query()->where('team_id', $this->teamId())->findOrFail($period);

        return response()->json(['data' => $action->handle($record, (int) auth()->id())]);
    }

    public function archive(Request $request, string $period, ArchiveYearEnd $action): JsonResponse
    {
        $record = YearEndPeriod::query()->where('team_id', $this->teamId())->findOrFail($period);

        return response()->json(['data' => $action->handle($record)]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

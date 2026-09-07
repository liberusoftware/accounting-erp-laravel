<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Depreciation\Actions\CreateDepreciationSchedule;
use Liberu\Accounting\Depreciation\Actions\PostDepreciationRun;
use Liberu\Accounting\Depreciation\Actions\RunDepreciation;
use Liberu\Accounting\Depreciation\Models\DepreciationRun;
use Liberu\Accounting\Depreciation\Models\DepreciationSchedule;
use Liberu\Accounting\Depreciation\Queries\DepreciationForecast;

final class DepreciationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => DepreciationSchedule::query()->where('team_id', $this->teamId())->latest()->paginate(min(100, max(1, $request->integer('per_page', 25))))]);
    }

    public function store(Request $request, CreateDepreciationSchedule $action): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['team_id'] = $this->teamId();

        return response()->json(['data' => $action->handle($data)], 201);
    }

    public function run(Request $request, string $schedule, RunDepreciation $action): JsonResponse
    {
        $scheduleRecord = DepreciationSchedule::query()->where('team_id', $this->teamId())->findOrFail($schedule);
        $data = $request->validate(['period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start']]);

        return response()->json(['data' => $action->handle($scheduleRecord, $data['period_start'], $data['period_end'])], 201);
    }

    public function post(Request $request, string $run, PostDepreciationRun $action): JsonResponse
    {
        $runRecord = DepreciationRun::query()->where('team_id', $this->teamId())->findOrFail($run);
        $data = $request->validate(['journal_ref' => ['required', 'string', 'max:160']]);

        return response()->json(['data' => $action->handle($runRecord, (int) auth()->id(), $data['journal_ref'])]);
    }

    public function forecast(DepreciationForecast $forecast): JsonResponse
    {
        return response()->json(['data' => $forecast->forTeam($this->teamId())->values()]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }

    private function rules(): array
    {
        return ['asset_ref' => ['required', 'string', 'max:160'], 'book_ref' => ['required', 'string', 'max:160'], 'method' => ['required', 'in:straight_line,declining_balance'], 'convention' => ['nullable', 'string', 'max:80'], 'useful_life_months' => ['required', 'integer', 'min:1'], 'cost' => ['required', 'numeric', 'gte:0'], 'residual_value' => ['nullable', 'numeric', 'gte:0'], 'start_date' => ['required', 'date'], 'currency' => ['required', 'string', 'size:3'], 'metadata' => ['nullable', 'array']];
    }
}

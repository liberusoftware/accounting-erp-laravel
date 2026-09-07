<?php

declare(strict_types=1);

namespace Liberu\Accounting\DashboardsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Dashboards\Actions\CreateDashboard;
use Liberu\Accounting\Dashboards\Actions\ShareDashboard;
use Liberu\Accounting\Dashboards\Actions\UpsertKpi;
use Liberu\Accounting\Dashboards\Models\Dashboard;
use Liberu\Accounting\Dashboards\Queries\DashboardQuery;

final class DashboardsController extends Controller
{
    public function index(DashboardQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateDashboard $action): JsonResponse
    {
        $data = $request->validate(['dashboard_ref' => ['required', 'string', 'max:160'], 'name' => ['required', 'string', 'max:160'], 'role' => ['nullable', 'string', 'max:120'], 'period' => ['nullable', 'string', 'max:80'], 'dimensions' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function kpi(Request $request, string $dashboard, UpsertKpi $action): JsonResponse
    {
        $record = Dashboard::query()->where('team_id', $this->teamId())->findOrFail($dashboard);
        $data = $request->validate(['kpi_ref' => ['required', 'string', 'max:160'], 'label' => ['required', 'string', 'max:160'], 'value' => ['required', 'numeric'], 'target' => ['nullable', 'numeric'], 'unit' => ['nullable', 'string', 'max:40'], 'drill_through' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle($record, $data)], 201);
    }

    public function share(Request $request, string $dashboard, ShareDashboard $action): JsonResponse
    {
        $record = Dashboard::query()->where('team_id', $this->teamId())->findOrFail($dashboard);
        $data = $request->validate(['shared_with_user_id' => ['nullable', 'integer'], 'shared_with_role' => ['nullable', 'string', 'max:120'], 'expires_at' => ['nullable', 'date']]);

        return response()->json(['data' => $action->handle($record, $data)], 201);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\RevenueRecognition\Actions\CreateRevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Actions\ModifyRevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Actions\RecognizeDueRevenue;
use Liberu\Accounting\RevenueRecognition\Actions\ReconcileRevenueRun;
use Liberu\Accounting\RevenueRecognition\Models\RevenueRecognitionRun;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;
use Liberu\Accounting\RevenueRecognitionApi\Http\Resources\RevenueScheduleResource;

final class RevenueRecognitionController extends Controller
{
    public function index(Request $request): mixed
    {
        return RevenueSchedule::query()->whereHas('obligation', fn ($query) => $query->where('team_id', $this->teamId($request)))->with(['entries', 'obligation'])->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateRevenueSchedule $action): mixed
    {
        return (new RevenueScheduleResource($action->handle([...$request->validate(['source_type' => 'nullable|string|max:80', 'source_id' => 'nullable|string|max:190', 'description' => 'nullable|string', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'total_amount' => 'required|numeric|min:0', 'start_date' => 'required|date', 'periods' => 'required|integer|min:1', 'deferred_account_ref' => 'required|string|max:190', 'revenue_account_ref' => 'required|string|max:190', 'funded' => 'boolean', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)])))->response()->setStatusCode(201);
    }

    public function show(Request $request, RevenueSchedule $schedule): RevenueScheduleResource
    {
        $this->assertTeam($request, $schedule);

        return new RevenueScheduleResource($schedule->load(['entries', 'modifications']));
    }

    public function recognize(Request $request, RevenueSchedule $schedule, RecognizeDueRevenue $action): mixed
    {
        $this->assertTeam($request, $schedule);
        $data = $request->validate(['as_of_date' => 'required|date']);

        return $action->handle($schedule, $data['as_of_date'], $this->teamId($request));
    }

    public function modify(Request $request, RevenueSchedule $schedule, ModifyRevenueSchedule $action): mixed
    {
        $this->assertTeam($request, $schedule);

        return $action->handle($schedule, $request->validate(['effective_date' => 'nullable|date', 'amount_delta' => 'required|numeric', 'reason' => 'required|string', 'metadata' => 'nullable|array']));
    }

    public function reconcile(Request $request, RevenueRecognitionRun $run, ReconcileRevenueRun $action): mixed
    {
        abort_unless((int) $run->team_id === $this->teamId($request), 404);

        return $action->handle($run, $request->validate(['references' => 'required|array'])['references']);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, RevenueSchedule $schedule): void
    {
        abort_unless((int) $schedule->obligation()->value('team_id') === $this->teamId($request), 404);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\TimeTracking\Actions\ApproveTimeEntry;
use Liberu\Accounting\TimeTracking\Actions\CreateTimeEntry;
use Liberu\Accounting\TimeTracking\Actions\StartTimer;
use Liberu\Accounting\TimeTracking\Actions\StopTimer;
use Liberu\Accounting\TimeTracking\Actions\SubmitTimeEntry;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;
use Liberu\Accounting\TimeTracking\Models\TimeTimer;

final class TimeTrackingController extends Controller
{
    public function entries(Request $request): mixed
    {
        return TimeEntry::query()->where('team_id', $this->teamId($request))->latest('worked_on')->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function createEntry(Request $request, CreateTimeEntry $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['worker_ref' => 'required|string|max:160', 'customer_ref' => 'nullable|string|max:160', 'project_ref' => 'nullable|string|max:160', 'task_ref' => 'nullable|string|max:160', 'worked_on' => 'required|date', 'hours' => 'required|numeric|gt:0|lte:24', 'billable_rate' => 'nullable|numeric|min:0', 'cost_rate' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|size:3|regex:/^[A-Z]{3}$/', 'billable' => 'nullable|boolean', 'description' => 'nullable|string', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function submit(Request $request, string $entry, SubmitTimeEntry $action): mixed
    {
        return $action->handle($this->entry($request, $entry));
    }

    public function approve(Request $request, string $entry, ApproveTimeEntry $action): mixed
    {
        return $action->handle($this->entry($request, $entry));
    }

    public function startTimer(Request $request, StartTimer $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['worker_ref' => 'required|string|max:160', 'project_ref' => 'nullable|string|max:160', 'started_at' => 'nullable|date']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function stopTimer(Request $request, string $timer, StopTimer $action): mixed
    {
        return $action->handle(TimeTimer::query()->where('team_id', $this->teamId($request))->findOrFail($timer));
    }

    private function entry(Request $request, string $entry): TimeEntry
    {
        return TimeEntry::query()->where('team_id', $this->teamId($request))->findOrFail($entry);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}

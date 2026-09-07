<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ProjectBilling\Actions\HandoffProjectBilling;
use Liberu\Accounting\ProjectBilling\Actions\RecordProjectBilling;
use Liberu\Accounting\ProjectBilling\Enums\BillingMethod;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;
use Liberu\Accounting\ProjectBilling\Queries\ProjectBillingSummary;

final class ProjectBillingController extends Controller
{
    public function index(Request $request): mixed
    {
        return ProjectBilling::query()->where('team_id', $this->teamId($request))->with('projectJob')->latest('period_start')->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, RecordProjectBilling $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['project_job_id' => 'required|integer', 'method' => 'required|string|in:'.implode(',', array_column(BillingMethod::cases(), 'value')), 'period_start' => 'nullable|date', 'period_end' => 'nullable|date', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'quantity' => 'nullable|numeric|min:0', 'rate' => 'nullable|numeric|min:0', 'amount' => 'nullable|numeric|min:0', 'progress_percent' => 'nullable|numeric|min:0|max:100', 'billable_time_amount' => 'nullable|numeric|min:0', 'billable_expense_amount' => 'nullable|numeric|min:0', 'retainer_amount' => 'nullable|numeric|min:0', 'write_up_down_amount' => 'nullable|numeric', 'source_ref' => 'nullable|string', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function show(Request $request, ProjectBilling $projectBilling): ProjectBilling
    {
        $this->assertTeam($request, $projectBilling);

        return $projectBilling->load('projectJob');
    }

    public function handoff(Request $request, ProjectBilling $projectBilling, HandoffProjectBilling $action): ProjectBilling
    {
        $this->assertTeam($request, $projectBilling);

        return $action->handle($projectBilling, $request->validate(['invoice_ref' => 'nullable|string'])['invoice_ref'] ?? null);
    }

    public function summary(Request $request, int $projectJobId, ProjectBillingSummary $query): array
    {
        return $query->forProject($projectJobId, $this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, ProjectBilling $billing): void
    {
        abort_unless((int) $billing->team_id === $this->teamId($request), 404);
    }
}

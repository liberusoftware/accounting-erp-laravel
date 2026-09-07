<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PayrollJournals\Actions\CreatePayrollJournal;
use Liberu\Accounting\PayrollJournals\Actions\PostPayrollJournal;
use Liberu\Accounting\PayrollJournals\Actions\ReversePayrollJournal;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;
use Liberu\Accounting\PayrollJournals\Queries\PayrollJournalSummary;
use Liberu\Accounting\PayrollJournalsApi\Http\Resources\PayrollJournalResource;

final class PayrollJournalsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PayrollJournalResource::collection(
            PayrollJournal::query()
                ->where('team_id', $this->teamId($request))
                ->latest('payroll_period_end')
                ->paginate(min(max($request->integer('per_page', 25), 1), 100)),
        );
    }

    public function store(Request $request, CreatePayrollJournal $action): JsonResponse
    {
        $attributes = $request->validate(['journal_ref' => 'required|string|max:150', 'payroll_period_start' => 'required|date', 'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'gross_pay' => 'required|numeric|gt:0', 'taxes' => 'nullable|numeric|gte:0', 'deductions' => 'nullable|numeric|gte:0', 'benefits' => 'nullable|numeric|gte:0', 'employer_costs' => 'nullable|numeric|gte:0', 'net_pay' => 'nullable|numeric|gte:0', 'liabilities' => 'nullable|array', 'allocation' => 'nullable|array', 'metadata' => 'nullable|array']);

        return (new PayrollJournalResource($action->handle([...$attributes, 'team_id' => $this->teamId($request)])))->response()->setStatusCode(201);
    }

    public function show(Request $request, PayrollJournal $payrollJournal): PayrollJournalResource
    {
        $this->assertTeam($request, $payrollJournal);

        return new PayrollJournalResource($payrollJournal);
    }

    public function post(Request $request, PayrollJournal $payrollJournal, PostPayrollJournal $action): PayrollJournalResource
    {
        $this->assertTeam($request, $payrollJournal);

        return new PayrollJournalResource($action->handle($payrollJournal));
    }

    public function reverse(Request $request, PayrollJournal $payrollJournal, ReversePayrollJournal $action): PayrollJournalResource
    {
        $this->assertTeam($request, $payrollJournal);

        return new PayrollJournalResource($action->handle($payrollJournal, $request->validate(['reversal_ref' => 'required|string|max:150'])['reversal_ref']));
    }

    public function summary(Request $request, PayrollJournalSummary $query): array
    {
        return $query->forTeam($this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, PayrollJournal $journal): void
    {
        abort_unless((int) $journal->team_id === $this->teamId($request), 404);
    }
}

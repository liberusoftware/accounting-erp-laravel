<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\EmployeeExpenses\Actions\AddExpenseItem;
use Liberu\Accounting\EmployeeExpenses\Actions\CreateExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\DecideExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\PostExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\ReimburseExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\SubmitExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Queries\ExpenseClaimQuery;
use Liberu\Accounting\EmployeeExpensesApi\Http\Resources\ExpenseClaimResource;

final class ExpenseClaimsController extends Controller
{
    public function __construct(private readonly ExpenseClaimQuery $query) {}

    public function index(Request $r): mixed
    {
        $s = $r->filled('status') ? ClaimStatus::tryFrom($r->string('status')->toString()) : null;
        abort_if($r->filled('status') && $s === null, 422, 'Unknown claim status.');

        return ExpenseClaimResource::collection($this->query->paginate($r->integer('team_id') ?: null, $s, $r->integer('per_page', 25)));
    }

    public function store(Request $r, CreateExpenseClaim $a): ExpenseClaimResource
    {
        $d = $r->validate(['team_id' => 'nullable|integer', 'employee_ref' => 'required|string|max:255', 'claim_ref' => 'required|string|max:100', 'currency' => 'required|string|size:3', 'project_ref' => 'nullable|string|max:255', 'dimension_ref' => 'nullable|array', 'metadata' => 'nullable|array']);
        $d['actor_ref'] = (string) $r->user()->getAuthIdentifier();

        return new ExpenseClaimResource($a->handle($d));
    }

    public function show(ExpenseClaim $c): ExpenseClaimResource
    {
        return new ExpenseClaimResource($c->load(['items', 'history']));
    }

    public function item(Request $r, ExpenseClaim $c, AddExpenseItem $a): ExpenseClaimResource
    {
        $d = $r->validate(['category_ref' => 'required|string|max:100', 'spent_on' => 'required|date', 'description' => 'required|string|max:5000', 'amount' => 'required|numeric|gt:0', 'tax_amount' => 'nullable|numeric|min:0', 'merchant' => 'nullable|string|max:255', 'receipt_ref' => 'nullable|string|max:255', 'per_diem_days' => 'nullable|numeric|gt:0', 'attendees' => 'nullable|array', 'metadata' => 'nullable|array']);
        $a->handle($c, $d);

        return new ExpenseClaimResource($c->load('items'));
    }

    public function submit(Request $r, ExpenseClaim $c, SubmitExpenseClaim $a): ExpenseClaimResource
    {
        return new ExpenseClaimResource($a->handle($c, (string) $r->user()->getAuthIdentifier()));
    }

    public function decide(Request $r, ExpenseClaim $c, DecideExpenseClaim $a): ExpenseClaimResource
    {
        $d = $r->validate(['approved' => 'required|boolean', 'reason' => 'nullable|string|max:5000']);

        return new ExpenseClaimResource($a->handle($c, (bool) $d['approved'], $d['reason'] ?? null, (string) $r->user()->getAuthIdentifier()));
    }

    public function reimburse(Request $r, ExpenseClaim $c, ReimburseExpenseClaim $a): ExpenseClaimResource
    {
        return new ExpenseClaimResource($a->handle($c, (string) $r->user()->getAuthIdentifier()));
    }

    public function post(Request $r, ExpenseClaim $c, PostExpenseClaim $a): ExpenseClaimResource
    {
        return new ExpenseClaimResource($a->handle($c, (string) $r->user()->getAuthIdentifier()));
    }
}

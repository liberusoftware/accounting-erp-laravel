<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\EmployeeExpenses\Actions\AddExpenseItem;
use Liberu\Accounting\EmployeeExpenses\Actions\CreateExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\DecideExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\PostExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\ReimburseExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Actions\SubmitExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Events\ClaimLifecycleChanged;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Tests\TestCase;

final class AccountingEmployeeExpensesTest extends TestCase
{
    use RefreshDatabase;

    public function test_claim_lifecycle_records_policy_evidence_and_history(): void
    {
        Event::fake();
        $claim = app(CreateExpenseClaim::class)->handle(['team_id' => 1, 'employee_ref' => 'EMP-1', 'claim_ref' => 'CL-001', 'currency' => 'gbp']);
        app(AddExpenseItem::class)->handle($claim, ['category_ref' => 'travel', 'spent_on' => '2026-08-25', 'description' => 'Rail fare', 'amount' => 125, 'receipt_ref' => 'receipt-1', 'attendees' => ['EMP-1']]);
        $claim = app(SubmitExpenseClaim::class)->handle($claim, 'EMP-1');
        $claim = app(DecideExpenseClaim::class)->handle($claim, true, null, 'manager-1');
        $claim = app(ReimburseExpenseClaim::class)->handle($claim, 'payroll-1');
        $claim = app(PostExpenseClaim::class)->handle($claim, 'ledger-1');

        $this->assertSame(ClaimStatus::Posted, $claim->status);
        $this->assertSame(125.0, (float) $claim->items()->sum('amount'));
        $this->assertSame(['created', 'submitted', 'approved', 'reimbursed', 'posted'], $claim->history()->orderBy('id')->pluck('event')->all());
        Event::assertDispatchedTimes(ClaimLifecycleChanged::class, 4);
    }

    public function test_claims_require_items_and_rejection_reasons(): void
    {
        $claim = app(CreateExpenseClaim::class)->handle(['employee_ref' => 'EMP-2', 'claim_ref' => 'CL-002', 'currency' => 'USD']);
        $this->expectException(InvalidClaim::class);
        app(SubmitExpenseClaim::class)->handle($claim);
    }

    public function test_api_write_scope_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['accounting.employee-expenses.read']);
        $this->postJson('/api/v1/accounting/employee-expenses', [])->assertForbidden();
    }
}

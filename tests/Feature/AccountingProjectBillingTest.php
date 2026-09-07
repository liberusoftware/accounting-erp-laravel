<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ProjectBilling\Actions\HandoffProjectBilling;
use Liberu\Accounting\ProjectBilling\Actions\RecordProjectBilling;
use Liberu\Accounting\ProjectBilling\Enums\BillingStatus;
use Liberu\Accounting\ProjectBilling\Exceptions\InvalidBilling;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;
use Liberu\Accounting\ProjectBilling\Queries\ProjectBillingSummary;

uses(RefreshDatabase::class);
it('records billing methods idempotently and hands off an invoice', function (): void {
    $action = app(RecordProjectBilling::class);
    $attributes = ['project_job_id' => 1, 'method' => 'time_material', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP', 'amount' => 500, 'billable_time_amount' => 400, 'billable_expense_amount' => 50, 'write_up_down_amount' => 25, 'source_ref' => 'BILL-1'];
    $record = $action->handle($attributes);
    $same = $action->handle(array_merge($attributes, ['amount' => 525]));
    expect($same->id)->toBe($record->id)->and(ProjectBilling::count())->toBe(1);
    $handoff = app(HandoffProjectBilling::class)->handle($record, 'INV-1');
    expect($handoff->status)->toBe(BillingStatus::HandedOff)->and(app(ProjectBillingSummary::class)->forProject(1)['billable_time_expense'])->toBe(475.0);
});
it('rejects invalid progress and cancelled handoff', function (): void {
    $action = app(RecordProjectBilling::class);
    expect(fn () => $action->handle(['project_job_id' => 1, 'method' => 'progress', 'progress_percent' => 101]))->toThrow(InvalidBilling::class);
    $record = ProjectBilling::create(['project_job_id' => 1, 'method' => 'fixed_fee', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'status' => BillingStatus::Cancelled]);
    expect(fn () => app(HandoffProjectBilling::class)->handle($record))->toThrow(InvalidBilling::class);
});

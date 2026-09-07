<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\RevenueRecognition\Actions\CreateRevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Actions\ModifyRevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Actions\RecognizeDueRevenue;
use Liberu\Accounting\RevenueRecognition\Actions\ReconcileRevenueRun;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionRunStatus;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;
use Liberu\Accounting\RevenueRecognition\Exceptions\InvalidRecognition;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;

uses(RefreshDatabase::class);

function recognitionSchedule(bool $funded = false): RevenueSchedule
{
    return app(CreateRevenueSchedule::class)->handle(['source_type' => 'invoice', 'source_id' => 'INV-1', 'currency' => 'USD', 'total_amount' => 100, 'start_date' => '2026-01-01', 'periods' => 3, 'deferred_account_ref' => 'deferred-1', 'revenue_account_ref' => 'revenue-1', 'funded' => $funded]);
}
it('preserves final-period rounding and waits for funded schedules', function (): void {
    $schedule = recognitionSchedule();
    expect($schedule->entries->pluck('amount')->map(fn ($amount) => (float) $amount)->all())->toBe([33.33, 33.33, 33.34]);
    $run = app(RecognizeDueRevenue::class)->handle($schedule, '2026-12-31');
    expect($run->status)->toBe(RecognitionRunStatus::Completed)->and($schedule->refresh()->entries()->where('status', RecognitionStatus::Completed)->count())->toBe(0);
});
it('recognizes funded due entries, completes, modifies, and reconciles', function (): void {
    $schedule = recognitionSchedule(true);
    $run = app(RecognizeDueRevenue::class)->handle($schedule, '2026-02-01');
    expect($run->processed_entries)->toBe(2)->and($schedule->refresh()->status)->toBe(RecognitionStatus::Active);
    app(ModifyRevenueSchedule::class)->handle($schedule->refresh(), ['amount_delta' => 10, 'reason' => 'Approved contract change']);
    $final = app(RecognizeDueRevenue::class)->handle($schedule->refresh(), '2026-04-01');
    expect($final->processed_entries)->toBe(1)->and($schedule->refresh()->status)->toBe(RecognitionStatus::Completed);
    expect(app(ReconcileRevenueRun::class)->handle($final, [['reference_id' => 'schedule-1', 'expected_amount' => 100, 'recognized_amount' => 100]])->status)->toBe(RecognitionRunStatus::Reconciled);
});
it('rejects invalid schedule invariants and completed modifications', function (): void {
    expect(fn () => recognitionSchedule(false) && app(CreateRevenueSchedule::class)->handle(['total_amount' => 1, 'start_date' => '2026-01-01', 'periods' => 0, 'deferred_account_ref' => 'a', 'revenue_account_ref' => 'b']))->toThrow(InvalidRecognition::class);
    $schedule = recognitionSchedule(true);
    app(RecognizeDueRevenue::class)->handle($schedule, '2027-01-01');
    expect(fn () => app(ModifyRevenueSchedule::class)->handle($schedule->refresh(), ['amount_delta' => 1, 'reason' => 'late']))->toThrow(InvalidRecognition::class);
});

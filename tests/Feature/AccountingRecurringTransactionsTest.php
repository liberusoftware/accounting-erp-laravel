<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\RecurringTransactions\Actions\ApproveRecurringTemplate;
use Liberu\Accounting\RecurringTransactions\Actions\CreateRecurringTemplate;
use Liberu\Accounting\RecurringTransactions\Actions\GenerateRecurringOccurrences;
use Liberu\Accounting\RecurringTransactions\Enums\OccurrenceStatus;
use Liberu\Accounting\RecurringTransactions\Enums\RecurringStatus;
use Liberu\Accounting\RecurringTransactions\Exceptions\InvalidRecurringTransaction;

uses(RefreshDatabase::class);
it('approves, catches up, and idempotently generates recurring occurrences', function (): void {
    $template = app(CreateRecurringTemplate::class)->handle(['name' => 'Monthly rent', 'transaction_type' => 'journal', 'frequency' => 'monthly', 'starts_on' => '2026-01-01', 'next_run_on' => '2026-01-01', 'automatic' => false, 'payload' => ['amount' => 100]]);
    app(ApproveRecurringTemplate::class)->handle($template, 7);
    $count = app(GenerateRecurringOccurrences::class)->handle($template, '2026-03-15');
    expect($count)->toBe(3)->and($template->refresh()->occurrences)->toHaveCount(3)->and($template->occurrences->where('status', OccurrenceStatus::Draft))->toHaveCount(3);
    expect(app(GenerateRecurringOccurrences::class)->handle($template, '2026-03-15'))->toBe(0);
});
it('supports automatic generation and expiry while rejecting invalid templates', function (): void {
    $template = app(CreateRecurringTemplate::class)->handle(['name' => 'Yearly', 'transaction_type' => 'bill', 'frequency' => 'yearly', 'starts_on' => '2026-01-01', 'ends_on' => '2026-01-01', 'automatic' => true, 'payload' => ['amount' => 10]]);
    app(ApproveRecurringTemplate::class)->handle($template);
    expect(app(GenerateRecurringOccurrences::class)->handle($template, '2027-01-02'))->toBe(1)->and($template->refresh()->status)->toBe(RecurringStatus::Expired)->and($template->occurrences->first()->status)->toBe(OccurrenceStatus::Generated);
    expect(fn () => app(CreateRecurringTemplate::class)->handle(['name' => 'bad', 'transaction_type' => 'x', 'frequency' => 'biweekly', 'starts_on' => '2026-01-01']))->toThrow(InvalidRecurringTransaction::class);
});

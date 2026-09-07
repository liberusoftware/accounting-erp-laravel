<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CashCollectionAssistant\Actions\CreateCashCollectionAssistant;
use Liberu\Accounting\CashCollectionAssistant\Actions\RecordCollectionPromise;
use Liberu\Accounting\CashCollectionAssistant\Actions\ScheduleCollectionReminder;
use Liberu\Accounting\CashCollectionAssistant\Exceptions\InvalidCashCollectionAssistant;

uses(RefreshDatabase::class);
it('prioritizes invoices and tracks reminders and promises', function (): void {
    $assistant = app(CreateCashCollectionAssistant::class)->handle(['team_id' => 909, 'invoice_ref' => 'INV-42', 'risk_score' => 80]);
    app(ScheduleCollectionReminder::class)->handle($assistant, '2026-09-01 09:00:00', 'Please settle the overdue balance.');
    $updated = app(RecordCollectionPromise::class)->handle($assistant->refresh(), '2026-09-15', 1250);
    expect($updated->risk_level)->toBe('high')->and($updated->reminder_status)->toBe('scheduled')->and($updated->promise_status)->toBe('open');
});
it('rejects risk scores outside the supported range', function (): void {
    expect(fn () => app(CreateCashCollectionAssistant::class)->handle(['team_id' => 909, 'invoice_ref' => 'INV-43', 'risk_score' => 101]))->toThrow(InvalidCashCollectionAssistant::class);
});

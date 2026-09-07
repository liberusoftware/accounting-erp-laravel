<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Collections\Actions\CreateCollectionCase;
use Liberu\Accounting\Collections\Actions\PrepareCollectionStatement;
use Liberu\Accounting\Collections\Actions\RecordCollectionActivity;
use Liberu\Accounting\Collections\Actions\SendToCollectionAgency;
use Liberu\Accounting\Collections\Actions\WriteOffCollectionCase;
use Liberu\Accounting\Collections\Exceptions\InvalidCollectionCase;

uses(RefreshDatabase::class);

it('records collection activity and escalates a case', function (): void {
    $case = app(CreateCollectionCase::class)->handle(['team_id' => 505, 'case_ref' => 'case-1', 'customer_ref' => 'customer-1', 'balance' => 1000, 'interest_rate' => 5]);
    $activity = app(RecordCollectionActivity::class);
    $activity->reminder($case, ['scheduled_for' => '2026-09-01', 'channel' => 'email']);
    $activity->promise($case->refresh(), ['due_on' => '2026-09-15', 'amount' => 500]);
    app(PrepareCollectionStatement::class)->handle($case->refresh(), ['period' => '2026-08']);
    app(SendToCollectionAgency::class)->handle($case->refresh(), 'agency-adapter');
    $writtenOff = app(WriteOffCollectionCase::class)->handle($case->refresh(), ['reason' => 'Unrecoverable', 'amount' => 1000]);

    expect($writtenOff->stage)->toBe('written-off')
        ->and($writtenOff->reminders)->toHaveCount(1)
        ->and($writtenOff->promise_to_pay['amount'])->toBe(500);
});

it('rejects negative collection balances', function (): void {
    expect(fn () => app(CreateCollectionCase::class)->handle(['team_id' => 505, 'case_ref' => 'case-2', 'customer_ref' => 'customer-2', 'balance' => -1]))
        ->toThrow(InvalidCollectionCase::class);
});

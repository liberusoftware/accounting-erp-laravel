<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CloseManagement\Actions\CertifyCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\CreateCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\LockCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\RecordCloseEvidence;
use Liberu\Accounting\CloseManagement\Actions\ReopenCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\UpdateCloseChecklist;
use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;
use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;

uses(RefreshDatabase::class);

it('certifies, locks and reopens a close cycle', function (): void {
    $cycle = app(CreateCloseCycle::class)->handle(['team_id' => 707, 'cycle_ref' => 'close-1', 'period' => '2026-08', 'due_date' => '2026-09-10']);
    app(UpdateCloseChecklist::class)->handle($cycle, ['reconcile-bank' => true]);
    app(RecordCloseEvidence::class)->handle($cycle->refresh(), ['reference' => 'evidence-1', 'type' => 'reconciliation']);
    app(CertifyCloseCycle::class)->handle($cycle->refresh(), ['certifier_ref' => 'user-1']);
    app(LockCloseCycle::class)->handle($cycle->refresh());
    $reopened = app(ReopenCloseCycle::class)->handle($cycle->refresh(), 'Late adjustment discovered');

    expect($reopened->status)->toBe(CloseCycleStatus::Reopened)
        ->and($reopened->review['reopen_reason'])->toBe('Late adjustment discovered');
});

it('requires checklist and evidence before certification', function (): void {
    $cycle = app(CreateCloseCycle::class)->handle(['team_id' => 707, 'cycle_ref' => 'close-2', 'period' => '2026-09', 'due_date' => '2026-10-10']);

    expect(fn () => app(CertifyCloseCycle::class)->handle($cycle, ['certifier_ref' => 'user-1']))
        ->toThrow(InvalidCloseCycle::class);
});

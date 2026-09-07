<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\YearEnd\Actions\AddYearEndAdjustment;
use Liberu\Accounting\YearEnd\Actions\ArchiveYearEnd;
use Liberu\Accounting\YearEnd\Actions\CreateYearEndPeriod;
use Liberu\Accounting\YearEnd\Actions\LockYearEnd;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;

uses(RefreshDatabase::class);

it('records adjustments, rolls retained earnings, locks and archives a period', function (): void {
    $period = app(CreateYearEndPeriod::class)->handle(['team_id' => 42, 'period_ref' => '2026', 'period_end' => '2026-12-31', 'opening_balances' => ['cash' => 1000], 'statutory_handoff' => ['status' => 'pending']]);
    app(AddYearEndAdjustment::class)->handle($period, ['adjustment_ref' => 'ADJ-1', 'amount' => 250, 'description' => 'Accrual', 'evidence' => ['file' => 'accrual.pdf']]);
    $locked = app(LockYearEnd::class)->handle($period->refresh(), 7);
    $archived = app(ArchiveYearEnd::class)->handle($locked);

    expect((float) $locked->retained_earnings)->toBe(250.0)->and($archived->status)->toBe(YearEndStatus::Archived);
});

it('prevents adjustments after locking', function (): void {
    $period = app(CreateYearEndPeriod::class)->handle(['team_id' => 42, 'period_ref' => '2027', 'period_end' => '2027-12-31']);
    app(LockYearEnd::class)->handle($period, 7);
    expect(fn () => app(AddYearEndAdjustment::class)->handle($period->refresh(), ['adjustment_ref' => 'ADJ-2', 'amount' => 1, 'description' => 'Late']))->toThrow(InvalidYearEnd::class);
});

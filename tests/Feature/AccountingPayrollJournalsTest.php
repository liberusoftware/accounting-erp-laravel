<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\PayrollJournals\Actions\CreatePayrollJournal;
use Liberu\Accounting\PayrollJournals\Actions\PostPayrollJournal;
use Liberu\Accounting\PayrollJournals\Actions\ReversePayrollJournal;
use Liberu\Accounting\PayrollJournals\Enums\JournalStatus;
use Liberu\Accounting\PayrollJournals\Exceptions\InvalidPayrollJournal;
use Liberu\Accounting\PayrollJournals\Queries\PayrollJournalSummary;

uses(RefreshDatabase::class);
it('validates payroll arithmetic and supports posting and reversal', function (): void {
    $journal = app(CreatePayrollJournal::class)->handle(['team_id' => 1, 'journal_ref' => 'J-1', 'payroll_period_start' => '2026-01-01', 'payroll_period_end' => '2026-01-31', 'gross_pay' => 1000, 'taxes' => 150, 'deductions' => 50, 'benefits' => 25, 'employer_costs' => 100, 'currency' => 'GBP']);
    $journal = app(PostPayrollJournal::class)->handle($journal);
    $journal = app(ReversePayrollJournal::class)->handle($journal, 'REV-1');
    expect($journal->status)->toBe(JournalStatus::Reversed)->and(app(PayrollJournalSummary::class)->forTeam(1)['net_pay'])->toBe(800.0);
});
it('rejects inconsistent net pay and invalid post transitions', function (): void {
    expect(fn () => app(CreatePayrollJournal::class)->handle(['team_id' => 1, 'journal_ref' => 'BAD', 'gross_pay' => 100, 'taxes' => 20, 'deductions' => 10, 'net_pay' => 99, 'payroll_period_start' => '2026-01-01', 'payroll_period_end' => '2026-01-31']))->toThrow(InvalidPayrollJournal::class);
    $journal = app(CreatePayrollJournal::class)->handle(['team_id' => 1, 'journal_ref' => 'J-2', 'gross_pay' => 100, 'payroll_period_start' => '2026-01-01', 'payroll_period_end' => '2026-01-31']);
    expect(fn () => app(ReversePayrollJournal::class)->handle($journal, 'REV'))->toThrow(InvalidPayrollJournal::class);
});

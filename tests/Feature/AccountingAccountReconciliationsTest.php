<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\AccountReconciliations\Actions\CarryForwardAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\CertifyAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\CreateAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\PrepareAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\ReviewAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;

uses(RefreshDatabase::class);

it('supports the preparation review certification and carry-forward lifecycle', function (): void {
    $reconciliation = app(CreateAccountReconciliation::class)->handle([
        'team_id' => 101,
        'book_id' => 10,
        'account_id' => 200,
        'period_start' => '2026-08-01',
        'period_end' => '2026-08-31',
    ]);

    $reconciliation = app(PrepareAccountReconciliation::class)->handle(
        $reconciliation,
        ['user_id' => 1],
        ['amount' => 1250.50, 'currency' => 'USD'],
        [['type' => 'statement', 'reference' => 'august-2026']],
    );
    expect($reconciliation->status)->toBe(ReconciliationStatus::Prepared);

    $reconciliation = app(ReviewAccountReconciliation::class)->handle($reconciliation, ['user_id' => 2, 'comment' => 'Reviewed']);
    expect($reconciliation->status)->toBe(ReconciliationStatus::InReview);

    $reconciliation = app(CertifyAccountReconciliation::class)->handle($reconciliation, ['user_id' => 2, 'attestation' => 'Balance agrees to supporting evidence.']);
    expect($reconciliation->status)->toBe(ReconciliationStatus::Certified)
        ->and($reconciliation->isCertified())->toBeTrue();

    $carriedForward = app(CarryForwardAccountReconciliation::class)->handle($reconciliation, [
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'amount' => 1250.50,
    ]);

    expect($carriedForward->status)->toBe(ReconciliationStatus::CarriedForward)
        ->and($carriedForward->carry_forward['period_start'])->toBe('2026-09-01');
});

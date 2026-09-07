<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\TaxReturns\Actions\AmendTaxReturn;
use Liberu\Accounting\TaxReturns\Actions\CreateTaxReturn;
use Liberu\Accounting\TaxReturns\Actions\SubmitTaxReturn;
use Liberu\Accounting\TaxReturns\Enums\TaxReturnStatus;
use Liberu\Accounting\TaxReturns\Exceptions\InvalidTaxReturn;

uses(RefreshDatabase::class);

it('supports tax return submission and amendment', function (): void {
    $taxReturn = app(CreateTaxReturn::class)->handle(['team_id' => 12, 'tax_type' => 'vat', 'jurisdiction' => 'GB', 'period_start' => '2026-04-01', 'period_end' => '2026-06-30']);
    app(SubmitTaxReturn::class)->handle($taxReturn, 'HMRC-1');
    $taxReturn->update(['status' => TaxReturnStatus::Accepted]);
    app(AmendTaxReturn::class)->handle($taxReturn->fresh());

    expect($taxReturn->fresh()->status)->toBe(TaxReturnStatus::Amended)
        ->and($taxReturn->fresh()->external_reference)->toBe('HMRC-1');
});

it('rejects an invalid return period', function (): void {
    expect(fn (): mixed => app(CreateTaxReturn::class)->handle(['team_id' => 12, 'tax_type' => 'vat', 'jurisdiction' => 'GB', 'period_start' => '2026-06-30', 'period_end' => '2026-04-01']))->toThrow(InvalidTaxReturn::class);
});

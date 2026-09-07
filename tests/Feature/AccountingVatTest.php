<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Vat\Actions\AddVatAdjustment;
use Liberu\Accounting\Vat\Actions\CreateVatReturn;
use Liberu\Accounting\Vat\Actions\RecordVatDigitalEvidence;
use Liberu\Accounting\Vat\Actions\RecordVatTransaction;
use Liberu\Accounting\Vat\Actions\SubmitVatReturn;
use Liberu\Accounting\Vat\Enums\VatDirection;
use Liberu\Accounting\Vat\Enums\VatReturnStatus;
use Liberu\Accounting\Vat\Exceptions\InvalidVat;
use Liberu\Accounting\Vat\Queries\VatReturnBoxes;

uses(RefreshDatabase::class);

it('records VAT, digital evidence, boxes, adjustments and submission', function (): void {
    $record = app(RecordVatTransaction::class)->handle(['team_id' => 12, 'direction' => VatDirection::Output, 'tax_code' => 'S20', 'net_amount' => 100, 'tax_amount' => 20, 'tax_rate' => 20, 'box' => 1, 'occurred_on' => '2026-08-01']);
    $return = app(CreateVatReturn::class)->handle(['team_id' => 12, 'period_start' => '2026-08-01', 'period_end' => '2026-08-31']);
    app(RecordVatDigitalEvidence::class)->handle($record, ['source' => 'invoice-1']);
    app(AddVatAdjustment::class)->handle($return, ['box' => 1, 'amount' => 2, 'reason' => 'Rounding']);
    $submitted = app(SubmitVatReturn::class)->handle($return, 'vat-2026-08');

    expect($record->direction)->toBe(VatDirection::Output)
        ->and($record->digitalRecord()->exists())->toBeTrue()
        ->and(app(VatReturnBoxes::class)->handle($return))->toHaveKey(1)
        ->and($return->adjustments()->count())->toBe(1)
        ->and($submitted->status)->toBe(VatReturnStatus::Submitted);
});

it('rejects invalid VAT transactions and return transitions', function (): void {
    expect(fn (): mixed => app(RecordVatTransaction::class)->handle(['team_id' => 12, 'direction' => 'invalid', 'tax_code' => 'S20', 'net_amount' => 1, 'tax_amount' => 1, 'occurred_on' => '2026-08-01']))->toThrow(InvalidVat::class);
});

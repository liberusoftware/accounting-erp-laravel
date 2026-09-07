<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\WithholdingTax\Actions\CalculateWithholdingTax;
use Liberu\Accounting\WithholdingTax\Actions\CreateWithholdingTaxRule;
use Liberu\Accounting\WithholdingTax\Actions\RemitWithholdingTax;
use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;
use Liberu\Accounting\WithholdingTax\Exceptions\InvalidWithholdingTax;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxLiability;

uses(RefreshDatabase::class);

it('calculates and remits a team withholding tax liability', function (): void {
    $rule = app(CreateWithholdingTaxRule::class)->handle([
        'team_id' => 42,
        'code' => 'CA-WHT',
        'name' => 'Canadian withholding',
        'jurisdiction' => 'CA',
        'rate' => 10,
        'threshold' => 100,
        'effective_from' => '2026-01-01',
    ]);

    $deduction = app(CalculateWithholdingTax::class)->handle($rule, [
        'party_type' => 'supplier',
        'party_id' => 'supplier-1',
        'source_ref' => 'bill-1',
        'currency' => 'CAD',
        'gross_amount' => 250,
    ]);

    expect($deduction->team_id)->toBe(42)
        ->and((float) $deduction->withheld_amount)->toBe(25.0)
        ->and($deduction->status)->toBe(WithholdingStatus::Calculated);

    $liability = WithholdingTaxLiability::query()->create([
        'team_id' => 42,
        'deduction_id' => $deduction->id,
        'amount' => $deduction->withheld_amount,
        'due_on' => '2026-02-15',
        'status' => WithholdingStatus::Open,
    ]);

    $remittance = app(RemitWithholdingTax::class)->handle($liability, [
        'amount' => 25,
        'remitted_on' => '2026-02-02',
        'reference' => 'remit-1',
    ]);

    expect($remittance->status)->toBe(WithholdingStatus::Remitted)
        ->and($liability->fresh()->status)->toBe(WithholdingStatus::Remitted);
});

it('rejects an invalid withholding tax rate', function (): void {
    expect(fn (): mixed => app(CreateWithholdingTaxRule::class)->handle([
        'team_id' => 42,
        'code' => 'INVALID',
        'name' => 'Invalid rule',
        'jurisdiction' => 'CA',
        'rate' => 101,
        'effective_from' => '2026-01-01',
    ]))->toThrow(InvalidWithholdingTax::class);
});

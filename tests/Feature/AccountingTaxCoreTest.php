<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\TaxCore\Actions\ActivateTaxRule;
use Liberu\Accounting\TaxCore\Actions\CalculateTax;
use Liberu\Accounting\TaxCore\Actions\CaptureTaxEvidence;
use Liberu\Accounting\TaxCore\Actions\CreateTaxRule;
use Liberu\Accounting\TaxCore\Enums\TaxTreatment;
use Liberu\Accounting\TaxCore\Exceptions\InvalidTaxRule;
use Liberu\Accounting\TaxCore\Queries\ActiveTaxRuleQuery;

uses(RefreshDatabase::class);

it('creates, activates, selects, and calculates an exclusive tax rule', function (): void {
    $rule = app(CreateTaxRule::class)->handle(['code' => 'VAT20', 'name' => 'Standard VAT', 'tax_type' => 'vat', 'jurisdiction_code' => 'GB', 'rate' => 20, 'effective_from' => '2026-01-01', 'control_account_code' => '2200']);
    app(ActivateTaxRule::class)->handle($rule);
    $active = app(ActiveTaxRuleQuery::class)->handle('VAT20', 'GB', '2026-08-25');
    $calculation = app(CalculateTax::class)->handle($active, 100);

    expect($active?->id)->toBe($rule->id)->and($calculation)->toMatchArray(['net' => 100.0, 'tax' => 20.0, 'gross' => 120.0, 'rate' => 20.0, 'rounding_scale' => 2]);
});

it('calculates inclusive, exempt, and zero-rated treatment with the configured rounding scale', function (): void {
    $inclusive = app(CreateTaxRule::class)->handle(['code' => 'INC', 'name' => 'Inclusive', 'tax_type' => 'vat', 'rate' => 20, 'treatment' => TaxTreatment::Inclusive, 'effective_from' => '2026-01-01', 'rounding_scale' => 2]);
    $exempt = app(CreateTaxRule::class)->handle(['code' => 'EXEMPT', 'name' => 'Exempt', 'tax_type' => 'vat', 'rate' => 20, 'treatment' => TaxTreatment::Exempt, 'effective_from' => '2026-01-01']);

    expect(app(CalculateTax::class)->handle($inclusive, 120))->toMatchArray(['net' => 100.0, 'tax' => 20.0, 'gross' => 120.0])
        ->and(app(CalculateTax::class)->handle($exempt, 120))->toMatchArray(['net' => 120.0, 'tax' => 0.0, 'gross' => 120.0]);
});

it('rejects invalid ranges and deduplicates tax evidence', function (): void {
    expect(fn () => app(CreateTaxRule::class)->handle(['code' => 'BAD', 'name' => 'Bad', 'tax_type' => 'vat', 'rate' => 101, 'effective_from' => '2026-01-01']))->toThrow(InvalidTaxRule::class);
    $rule = app(CreateTaxRule::class)->handle(['code' => 'EVIDENCE', 'name' => 'Evidence', 'tax_type' => 'vat', 'rate' => 0, 'effective_from' => '2026-01-01']);
    app(CaptureTaxEvidence::class)->handle($rule, 'tax-authority', 'gb-vat-2026', ['rate' => 20], 4);
    app(CaptureTaxEvidence::class)->handle($rule->refresh(), 'tax-authority', 'gb-vat-2026', ['rate' => 20], 4);

    expect($rule->refresh()->evidence)->toHaveCount(1)->and($rule->evidence->firstOrFail()->snapshot_hash)->toHaveLength(64);
});

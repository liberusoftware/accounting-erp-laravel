<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\MultiCurrency\Actions\ConfigureCurrency;
use Liberu\Accounting\MultiCurrency\Actions\CreateRevaluation;
use Liberu\Accounting\MultiCurrency\Actions\RecordExchangeRate;
use Liberu\Accounting\MultiCurrency\Enums\CurrencyRole;
use Liberu\Accounting\MultiCurrency\Enums\GainStatus;
use Liberu\Accounting\MultiCurrency\Enums\RevaluationStatus;
use Liberu\Accounting\MultiCurrency\Exceptions\InvalidCurrency;
use Liberu\Accounting\MultiCurrency\Queries\CurrencyQuery;

uses(RefreshDatabase::class);

it('configures currency roles, stores historical rates, and calculates realized and unrealized gains', function (): void {
    $profile = app(ConfigureCurrency::class)->handle(['team_id' => 1, 'scope_ref' => 'entity-uk', 'currency' => 'GBP', 'role' => 'functional']);
    $reporting = app(ConfigureCurrency::class)->handle(['team_id' => 1, 'scope_ref' => 'entity-uk', 'currency' => 'EUR', 'role' => 'reporting']);
    $rate = app(RecordExchangeRate::class)->handle(['team_id' => 1, 'from_currency' => 'USD', 'to_currency' => 'GBP', 'rate_date' => '2026-01-01', 'rate' => 0.8, 'source' => 'historical']);
    $run = app(CreateRevaluation::class)->handle(['team_id' => 1, 'run_ref' => 'FX-2026-01', 'scope_ref' => 'entity-uk', 'as_of_date' => '2026-01-31', 'functional_currency' => 'GBP'], [
        ['reference_type' => 'receivable', 'reference_id' => 'AR-1', 'currency' => 'USD', 'foreign_amount' => 100, 'book_rate' => 0.8, 'closing_rate' => 0.85, 'gain_status' => 'unrealized'],
        ['reference_type' => 'bank', 'reference_id' => 'BANK-1', 'currency' => 'EUR', 'foreign_amount' => 50, 'book_rate' => 0.8, 'closing_rate' => 0.75, 'gain_status' => 'realized'],
    ]);
    expect($profile->role)->toBe(CurrencyRole::Functional)->and($reporting->currency)->toBe('EUR')->and($rate->rate)->toEqual('0.8000000000')->and($run->status)->toBe(RevaluationStatus::Calculated)->and($run->summary['net_unrealized'])->toEqual(5.0)->and($run->summary['net_realized'])->toEqual(-2.5)->and($run->positions->where('gain_status', GainStatus::Unrealized)->count())->toBe(1);
});

it('rejects invalid rates and is idempotent for revaluation references', function (): void {
    expect(fn (): mixed => app(RecordExchangeRate::class)->handle(['from_currency' => 'USD', 'to_currency' => 'USD', 'rate_date' => '2026-01-01', 'rate' => 1]))->toThrow(InvalidCurrency::class);
    $data = ['run_ref' => 'FX-1', 'as_of_date' => '2026-01-01', 'functional_currency' => 'GBP'];
    $positions = [['reference_id' => 'AR-1', 'currency' => 'USD', 'foreign_amount' => 10, 'book_rate' => .8, 'closing_rate' => .9]];
    $first = app(CreateRevaluation::class)->handle($data, $positions);
    $same = app(CreateRevaluation::class)->handle($data, $positions);
    expect($same->id)->toBe($first->id);
    expect(fn (): mixed => app(CreateRevaluation::class)->handle($data, array_merge($positions, [['reference_id' => 'AR-2', 'currency' => 'USD', 'foreign_amount' => 5, 'book_rate' => .8, 'closing_rate' => .9]])))->toThrow(InvalidCurrency::class);
});

it('exposes the authenticated multi-currency API', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.multi-currency.read', 'accounting.multi-currency.write']);
    $this->postJson('/api/v1/accounting/multi-currency/rates', ['from_currency' => 'USD', 'to_currency' => 'GBP', 'rate_date' => '2026-01-01', 'rate' => .8])->assertCreated();
    $this->getJson('/api/v1/accounting/multi-currency/rates')->assertOk();
    expect(app(CurrencyQuery::class)->rates(2)->total())->toBe(0);
});

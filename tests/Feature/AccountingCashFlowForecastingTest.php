<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CashFlowForecasting\Actions\AddForecastItem;
use Liberu\Accounting\CashFlowForecasting\Actions\CreateCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Actions\SetForecastScenario;
use Liberu\Accounting\CashFlowForecasting\Actions\UpdateCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Exceptions\InvalidCashFlowForecast;

uses(RefreshDatabase::class);

it('builds a cash-flow forecast with timing, scenarios and confidence', function (): void {
    $forecast = app(CreateCashFlowForecast::class)->handle(['team_id' => 1001, 'forecast_ref' => 'forecast-1', 'currency' => 'GBP', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-31', 'opening_cash' => 1000]);
    $items = app(AddForecastItem::class);
    $items->receivable($forecast, ['due_on' => '2026-09-15', 'amount' => 500]);
    $items->payable($forecast->refresh(), ['due_on' => '2026-09-20', 'amount' => 200]);
    app(SetForecastScenario::class)->handle($forecast->refresh(), ['name' => 'Base', 'cash_adjustment' => 300]);
    $updated = app(UpdateCashFlowForecast::class)->handle($forecast->refresh(), ['confidence' => .85, 'forecast_cash' => 1600]);

    expect((float) $updated->confidence)->toBe(.85)
        ->and($updated->receivables)->toHaveCount(1)
        ->and($updated->scenarios)->toHaveCount(1);
});

it('rejects invalid forecast dates and confidence', function (): void {
    expect(fn () => app(CreateCashFlowForecast::class)->handle(['team_id' => 1001, 'forecast_ref' => 'bad', 'currency' => 'GBP', 'starts_on' => '2026-12-31', 'ends_on' => '2026-09-01']))
        ->toThrow(InvalidCashFlowForecast::class);
});

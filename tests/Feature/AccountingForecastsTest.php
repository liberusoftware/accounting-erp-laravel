<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Forecasts\Actions\AddForecastLine;
use Liberu\Accounting\Forecasts\Actions\CreateForecast;
use Liberu\Accounting\Forecasts\Actions\DecideForecast;
use Liberu\Accounting\Forecasts\Actions\ReplaceActual;
use Liberu\Accounting\Forecasts\Actions\SubmitForecast;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Queries\ForecastQuery;
use Tests\TestCase;

final class AccountingForecastsTest extends TestCase
{
    use RefreshDatabase;

    public function test_forecast_supports_lines_actual_replacement_approval_and_variance(): void
    {
        $forecast = app(CreateForecast::class)->handle(['forecast_ref' => 'FC-001', 'name' => 'Q4 outlook', 'currency' => 'GBP', 'method' => 'driver', 'base_period' => '2026-10', 'horizon_periods' => 3, 'scenario_ref' => 'base']);
        $line = app(AddForecastLine::class)->handle($forecast, ['period_ref' => '2026-10', 'account_ref' => 'sales', 'description' => 'Sales', 'driver_ref' => 'units', 'forecast_value' => 1000]);
        $forecast = app(SubmitForecast::class)->handle($forecast);
        $forecast = app(DecideForecast::class)->handle($forecast, 'reviewer-1', true);
        app(ReplaceActual::class)->handle($forecast, ['line_id' => $line->getKey(), 'period_ref' => '2026-10', 'actual_value' => 900, 'source_ref' => 'ledger-1']);

        $variance = app(ForecastQuery::class)->variance($forecast);
        $this->assertSame(ForecastStatus::Approved, $forecast->status);
        $this->assertSame(100.0, $variance['variance_total']);
        $this->assertSame(900.0, $variance['actual_total']);
    }

    public function test_forecast_cannot_be_submitted_without_lines(): void
    {
        $forecast = app(CreateForecast::class)->handle(['forecast_ref' => 'FC-002', 'name' => 'Empty', 'currency' => 'USD', 'method' => 'manual', 'base_period' => '2026-10', 'horizon_periods' => 1]);
        $this->expectException(InvalidForecast::class);
        app(SubmitForecast::class)->handle($forecast);
    }
}

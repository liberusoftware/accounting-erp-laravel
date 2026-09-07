<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Liberu\Accounting\Forecasts\Actions\AddForecastAssumption;
use Liberu\Accounting\Forecasts\Actions\CreateForecast;
use Liberu\Accounting\Forecasts\Actions\CreateForecastPeriods;
use Liberu\Accounting\Forecasts\Actions\SubmitForecast;
use Liberu\Accounting\Forecasts\Events\ForecastSubmitted;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Tests\TestCase;

final class AccountingForecastsBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_forecast_can_generate_rolling_periods_and_assumptions(): void
    {
        $forecast = app(CreateForecast::class)->handle(['forecast_ref' => 'FC-BOUNDARY', 'name' => 'Rolling outlook', 'currency' => 'usd', 'method' => 'driver', 'base_period' => '2026-10', 'horizon_periods' => 3]);

        app(CreateForecastPeriods::class)->handle($forecast);
        app(AddForecastAssumption::class)->handle($forecast, ['assumption_ref' => 'growth', 'name' => 'Growth', 'value' => 5, 'unit' => 'percent', 'source' => 'planning', 'effective_from' => '2026-10-01']);

        $this->assertSame(['2026-10', '2026-11', '2026-12'], $forecast->periods()->orderBy('starts_on')->pluck('period_ref')->all());
        $this->assertSame('5.000000', $forecast->assumptions()->first()->value);
    }

    public function test_period_generation_and_assumptions_are_rejected_after_submission(): void
    {
        $forecast = app(CreateForecast::class)->handle(['forecast_ref' => 'FC-LOCKED', 'name' => 'Locked', 'currency' => 'USD', 'method' => 'manual', 'base_period' => '2026-10', 'horizon_periods' => 1]);
        $forecast->lines()->create(['period_ref' => '2026-10', 'account_ref' => 'sales', 'description' => 'Sales', 'forecast_value' => 100]);
        Event::fake();
        $forecast = app(SubmitForecast::class)->handle($forecast);
        Event::assertDispatched(ForecastSubmitted::class);

        $this->expectException(InvalidForecast::class);
        app(CreateForecastPeriods::class)->handle($forecast);
    }
}

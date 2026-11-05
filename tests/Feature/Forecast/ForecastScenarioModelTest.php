<?php

// src/tests/Feature/Forecast/ForecastScenarioModelTest.php
declare(strict_types=1);

namespace Tests\Feature\Forecast;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\ForecastScenario;
use Liberu\Accounting\Erp\Models\ForecastScenarioLine;
use Tests\TestCase;

class ForecastScenarioModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_scenario_has_type_factor_lines(): void
    {
        $scenario = ForecastScenario::create(['name' => 'Optimistic']);
        ForecastScenarioLine::create([
            'forecast_scenario_id' => $scenario->id, 'account_type' => 'revenue', 'adjustment_pct' => 10,
        ]);

        $this->assertCount(1, $scenario->fresh()->lines);
        $this->assertSame('revenue', $scenario->lines->first()->account_type);
    }
}

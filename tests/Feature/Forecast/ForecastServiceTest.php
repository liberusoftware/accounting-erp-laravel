<?php

// src/tests/Feature/Forecast/ForecastServiceTest.php
declare(strict_types=1);

namespace Tests\Feature\Forecast;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\ForecastScenario;
use Liberu\Accounting\Erp\Models\ForecastScenarioLine;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\Transaction;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\ForecastService;
use Tests\TestCase;

class ForecastServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedTeamActuals(int $teamId): void
    {
        // Ensure team exists for FK constraint
        Team::forceCreate(['id' => $teamId, 'user_id' => User::factory()->create()->id, 'name' => "Team $teamId", 'personal_team' => false]);

        $revenue = Account::factory()->create(['team_id' => $teamId, 'account_type' => 'revenue']);
        $expense = Account::factory()->create(['team_id' => $teamId, 'account_type' => 'expense']);
        foreach ([100, 200, 300] as $i => $amt) {
            Transaction::create(['account_id' => $revenue->id, 'transaction_date' => '2026-0'.($i + 1).'-01', 'amount' => $amt]);
        }
        Transaction::create(['account_id' => $expense->id, 'transaction_date' => '2026-01-01', 'amount' => 50]);
    }

    public function test_rolling_baseline_averages_actuals_per_type(): void
    {
        $this->seedTeamActuals(7);
        $baseline = app(ForecastService::class)->rollingBaseline(2, 7);

        // revenue avg (100+200+300)/3 = 200, carried across 2 periods; expense = 50; cogs = 0.
        $this->assertSame([1 => 200.0, 2 => 200.0], $baseline['revenue']);
        $this->assertSame([1 => 50.0, 2 => 50.0], $baseline['expense']);
        $this->assertSame([1 => 0.0, 2 => 0.0], $baseline['cost_of_goods_sold']);
    }

    public function test_apply_scenario_adjusts_by_type_factor(): void
    {
        $baseline = ['revenue' => [1 => 200.0], 'cost_of_goods_sold' => [1 => 0.0], 'expense' => [1 => 50.0]];
        $scenario = ForecastScenario::create(['name' => 'S']);
        ForecastScenarioLine::create(['forecast_scenario_id' => $scenario->id, 'account_type' => 'revenue', 'adjustment_pct' => 10]);
        ForecastScenarioLine::create(['forecast_scenario_id' => $scenario->id, 'account_type' => 'expense', 'adjustment_pct' => -20]);

        $adjusted = app(ForecastService::class)->applyScenario($baseline, $scenario);

        $this->assertSame([1 => 220.0], $adjusted['revenue']);   // +10%
        $this->assertSame([1 => 40.0], $adjusted['expense']);    // -20%
        $this->assertSame([1 => 0.0], $adjusted['cost_of_goods_sold']); // no line → unchanged
    }

    public function test_compare_reports_net_income_for_both(): void
    {
        $this->seedTeamActuals(7);
        $scenario = ForecastScenario::create(['name' => 'S', 'team_id' => 7]);
        ForecastScenarioLine::create(['forecast_scenario_id' => $scenario->id, 'account_type' => 'revenue', 'adjustment_pct' => 10]);

        $result = app(ForecastService::class)->compare(1, $scenario, 7);

        // baseline net = 200 - 0 - 50 = 150; scenario net = 220 - 0 - 50 = 170.
        $this->assertSame(150.0, $result['baseline_net_income'][1]);
        $this->assertSame(170.0, $result['scenario_net_income'][1]);
    }

    public function test_no_actuals_yields_zero(): void
    {
        $baseline = app(ForecastService::class)->rollingBaseline(1, 999);
        $this->assertSame([1 => 0.0], $baseline['revenue']);
    }
}

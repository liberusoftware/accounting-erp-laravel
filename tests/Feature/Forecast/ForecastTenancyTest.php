<?php

// src/tests/Feature/Forecast/ForecastTenancyTest.php
declare(strict_types=1);

namespace Tests\Feature\Forecast;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\ForecastScenario;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\Transaction;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\ErpFilament\Filament\App\Pages\ForecastComparison;
use Tests\TestCase;

class ForecastTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_scenario_stamps_actor_team(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);

        $scenario = ForecastScenario::create(['name' => 'Base case']);

        $this->assertSame($team->id, (int) $scenario->team_id);
    }

    public function test_comparison_page_uses_the_active_tenant_not_the_auth_team(): void
    {
        $user = User::factory()->create();
        $teamA = Team::forceCreate(['user_id' => $user->id, 'name' => 'A', 'personal_team' => false]);
        $teamB = Team::forceCreate(['user_id' => $user->id, 'name' => 'B', 'personal_team' => false]);

        // Auth current team is A, but the active Filament tenant is B — they can drift.
        $user->forceFill(['current_team_id' => $teamA->id])->save();
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($teamB);

        $accB = Account::factory()->create(['team_id' => $teamB->id, 'account_type' => 'revenue']);
        Transaction::create(['account_id' => $accB->id, 'transaction_date' => '2026-01-01', 'amount' => 500]);
        $accA = Account::factory()->create(['team_id' => $teamA->id, 'account_type' => 'revenue']);
        Transaction::create(['account_id' => $accA->id, 'transaction_date' => '2026-01-01', 'amount' => 999]);

        $scenario = ForecastScenario::create(['name' => 'S', 'team_id' => $teamB->id]);

        $page = new ForecastComparison();
        $page->data = ['forecast_scenario_id' => $scenario->id, 'periods' => 1];
        $page->generate();

        // Must be team B's actuals (500), never team A's (999).
        $this->assertSame([1 => 500.0], $page->result['baseline']['revenue']);
    }
}

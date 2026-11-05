<?php

declare(strict_types=1);

namespace Tests\Feature\Consolidation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\ErpFilament\Filament\App\Pages\ConsolidatedReports;
use Tests\TestCase;

class ConsolidatedReportsTest extends TestCase
{
    use RefreshDatabase;

    private function team(User $owner): Team
    {
        return Team::forceCreate(['user_id' => $owner->id, 'name' => 'T', 'personal_team' => false]);
    }

    public function test_generate_populates_the_three_consolidated_statements(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $a = $this->team($user);
        $b = $this->team($user);

        $group = ConsolidationGroup::create(['name' => 'G', 'owner_team_id' => $a->id]);
        $group->members()->sync([$a->id, $b->id]);

        $page = new ConsolidatedReports();
        $page->data = ['consolidation_group_id' => $group->id, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31'];
        $page->generate();

        $this->assertArrayHasKey('net_income', $page->profitAndLoss['consolidated']);
        $this->assertArrayHasKey('assets', $page->balanceSheet['consolidated']);
        $this->assertArrayHasKey('net_change_in_cash', $page->cashFlow['consolidated']);
    }

    public function test_group_visible_only_to_member_team_users(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $a = $this->team($owner);

        $group = ConsolidationGroup::create(['name' => 'G', 'owner_team_id' => $a->id]);
        $group->members()->sync([$a->id]);

        $this->actingAs($owner);
        $this->assertArrayHasKey($group->id, (new ConsolidatedReports())->visibleGroups());

        $this->actingAs($stranger);
        $this->assertArrayNotHasKey($group->id, (new ConsolidatedReports())->visibleGroups());
    }

    public function test_generate_ignores_a_group_the_user_cannot_see(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $a = $this->team($owner);
        $group = ConsolidationGroup::create(['name' => 'G', 'owner_team_id' => $a->id]);
        $group->members()->sync([$a->id]);

        $this->actingAs($stranger);
        $page = new ConsolidatedReports();
        $page->data = ['consolidation_group_id' => $group->id, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31'];
        $page->generate();

        $this->assertNull($page->profitAndLoss);
    }
}

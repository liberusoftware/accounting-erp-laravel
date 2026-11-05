<?php

declare(strict_types=1);

namespace Tests\Feature\Consolidation;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ConsolidationGroups\ConsolidationGroupResource;
use Tests\TestCase;

class ConsolidationGroupResourceTest extends TestCase
{
    use RefreshDatabase;

    private function team(User $owner, string $name): Team
    {
        return Team::forceCreate(['user_id' => $owner->id, 'name' => $name, 'personal_team' => false]);
    }

    public function test_query_is_scoped_to_the_current_tenant_owner_team(): void
    {
        $user = User::factory()->create();
        $teamA = $this->team($user, 'A');
        $teamB = $this->team($user, 'B');

        $ownedByA = ConsolidationGroup::create(['name' => 'GA', 'owner_team_id' => $teamA->id]);
        $ownedByB = ConsolidationGroup::create(['name' => 'GB', 'owner_team_id' => $teamB->id]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($teamA);

        $ids = ConsolidationGroupResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($ownedByA->id, $ids);
        $this->assertNotContains($ownedByB->id, $ids);
    }

    public function test_member_teams_persist_through_the_pivot(): void
    {
        $user = User::factory()->create();
        $a = $this->team($user, 'A');
        $b = $this->team($user, 'B');

        $group = ConsolidationGroup::create(['name' => 'G', 'owner_team_id' => $a->id]);
        $group->members()->sync([$a->id, $b->id]);

        $this->assertSame(2, $group->members()->count());
        $this->assertDatabaseHas('consolidation_group_team', [
            'consolidation_group_id' => $group->id,
            'team_id' => $b->id,
        ]);
    }

    public function test_enforce_allowed_members_drops_teams_the_user_cannot_add(): void
    {
        $user = User::factory()->create();
        $mine = $this->team($user, 'Mine');

        $stranger = User::factory()->create();
        $foreign = Team::forceCreate(['user_id' => $stranger->id, 'name' => 'Foreign', 'personal_team' => false]);

        $group = ConsolidationGroup::create(['name' => 'G', 'owner_team_id' => $mine->id]);
        // Simulate a crafted request attaching a team the user does not belong to.
        $group->members()->sync([$mine->id, $foreign->id]);

        $this->actingAs($user);
        ConsolidationGroupResource::enforceAllowedMembers($group);

        $ids = $group->members()->pluck('teams.id')->map(fn ($v): int => (int) $v)->all();
        $this->assertContains($mine->id, $ids);
        $this->assertNotContains($foreign->id, $ids);
    }
}

<?php

// src/tests/Feature/Procurement/PurchaseRequestTenancyTest.php
declare(strict_types=1);

namespace Tests\Feature\Procurement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\PurchaseRequest;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class PurchaseRequestTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_stamps_actor_team_on_create(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);

        $request = PurchaseRequest::create(['request_date' => '2026-07-01', 'total_amount' => 10, 'status' => 'draft']);

        $this->assertSame($team->id, (int) $request->team_id);
    }
}

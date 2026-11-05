<?php

// src/tests/Feature/Procurement/PurchaseRequestApprovalTest.php
declare(strict_types=1);

namespace Tests\Feature\Procurement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\ApprovalRule;
use Liberu\Accounting\Erp\Models\PurchaseRequest;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PurchaseRequestApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_auto_approves_when_no_spend_rule_matches(): void
    {
        // submitForApproval() fails closed without a team (Liberu\Accounting\Erp\Concerns\Approvable::submitForApproval),
        // so a team is required even for the no-rule-matches path.
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);

        $request = PurchaseRequest::create([
            'request_date' => '2026-07-01', 'total_amount' => 500, 'status' => 'draft', 'team_id' => $team->id,
        ]);

        $request->submitForApproval();

        $this->assertSame('approved', $request->fresh()->approval_status);
    }

    public function test_request_over_threshold_routes_to_pending(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        // The first approval step notifies users holding this Spatie role (see
        // ApprovalRequestedNotification::dispatchToRole), so it must exist first.
        Role::create(['name' => 'manager', 'guard_name' => 'web']);
        ApprovalRule::create([
            'team_id' => $team->id, 'approvable_type' => 'PurchaseRequest',
            'min_amount' => 100, 'steps' => ['manager'], 'is_active' => true,
        ]);

        $request = PurchaseRequest::create([
            'request_date' => '2026-07-01', 'total_amount' => 500, 'status' => 'draft', 'team_id' => $team->id,
        ]);

        $request->submitForApproval();

        $this->assertSame('pending', $request->fresh()->approval_status);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Approval;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Liberu\Accounting\Erp\Jobs\EscalateApprovalsJob;
use Liberu\Accounting\Erp\Models\ApprovalRule;
use Liberu\Accounting\Erp\Models\ApprovalStep;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\ApprovalService;
use Liberu\Accounting\Erp\Services\TeamManagementService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EscalateApprovalsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_escalates_overdue_step_and_lets_fallback_role_approve_but_never_auto_approves(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $team = app(TeamManagementService::class)->createPersonalTeamForUser($owner);
        $teamId = $team->getKey();

        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('finance_director', 'web');

        $fallbackUser = User::factory()->create(['current_team_id' => $teamId]);
        $fallbackUser->assignRole('finance_director');

        ApprovalRule::create([
            'team_id' => $teamId,
            'approvable_type' => 'Bill',
            'min_amount' => 0,
            'steps' => ['manager'],
            'deadline_days' => 1,
            'fallback_role' => 'finance_director',
            'is_active' => true,
        ]);

        $bill = Bill::factory()->create(['team_id' => $teamId, 'total_amount' => 9000, 'approval_status' => 'draft']);
        $bill->submitForApproval();

        $step = $bill->approvalRequest()->first()->steps()->first();
        $this->assertNotNull($step->deadline_at, 'step 1 got a deadline from the rule');

        $svc = app(ApprovalService::class);
        $this->assertFalse($svc->canAct($step, $fallbackUser), 'fallback role denied before escalation');

        // Force the deadline into the past so the job picks it up.
        $step->forceFill(['deadline_at' => now()->subDay()])->save();

        (new EscalateApprovalsJob())->handle();

        $step->refresh();
        $this->assertSame(ApprovalStep::STATUS_ESCALATED, $step->status);
        $this->assertNotNull($step->escalated_at);
        $this->assertSame('pending', $bill->fresh()->approval_status, 'the job itself never approves the document');

        $this->assertTrue($svc->canAct($step, $fallbackUser), 'fallback role can act once escalated');

        $svc->approve($step, $fallbackUser);
        $this->assertSame('approved', $bill->fresh()->approval_status);
    }

    public function test_ignores_steps_without_a_deadline_or_already_escalated(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $team = app(TeamManagementService::class)->createPersonalTeamForUser($owner);
        $teamId = $team->getKey();

        Role::findOrCreate('manager', 'web');

        ApprovalRule::create([
            'team_id' => $teamId,
            'approvable_type' => 'Bill',
            'min_amount' => 0,
            'steps' => ['manager'],
            'is_active' => true,
        ]);

        $bill = Bill::factory()->create(['team_id' => $teamId, 'total_amount' => 9000, 'approval_status' => 'draft']);
        $bill->submitForApproval();

        $step = $bill->approvalRequest()->first()->steps()->first();
        $this->assertNull($step->deadline_at, 'no deadline_days on the rule means no deadline');

        (new EscalateApprovalsJob())->handle();

        $this->assertSame(ApprovalStep::STATUS_PENDING, $step->fresh()->status);
    }
}

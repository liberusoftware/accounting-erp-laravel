<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class InvoiceApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_approve_and_reject_do_not_fatal_and_set_status(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);

        $approved = Invoice::factory()->create(['team_id' => $team->id]);
        $approved->approve(); // previously fatal: dispatched a nonexistent InvoiceApproved class
        $this->assertSame('approved', $approved->fresh()->approval_status);

        $rejected = Invoice::factory()->create(['team_id' => $team->id]);
        $rejected->reject('duplicate');
        $this->assertSame('rejected', $rejected->fresh()->approval_status);
        $this->assertSame('duplicate', $rejected->fresh()->rejection_reason);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\RevenueRecognition;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\RevenueSchedule;
use Liberu\Accounting\Erp\Models\RevenueScheduleEntry;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class RevenueScheduleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_owns_entries_and_links_invoice_and_accounts(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 1200]);
        $deferred = Account::create(['account_number' => 2400, 'account_name' => 'Deferred Revenue', 'account_type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id]);
        $revenue = Account::create(['account_number' => 4000, 'account_name' => 'Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'opening_balance' => 0, 'is_active' => true, 'allow_manual_entry' => true, 'user_id' => $user->id]);

        $schedule = RevenueSchedule::create([
            'invoice_id' => $invoice->id, 'total_amount' => 1200, 'start_date' => '2026-01-01',
            'periods' => 12, 'deferred_account_id' => $deferred->id, 'revenue_account_id' => $revenue->id,
            'status' => 'active', 'team_id' => $team->id,
        ]);
        $entry = RevenueScheduleEntry::create([
            'revenue_schedule_id' => $schedule->id, 'period_number' => 1,
            'recognition_date' => '2026-01-01', 'amount' => 100, 'recognized' => false,
        ]);

        $this->assertTrue($schedule->invoice->is($invoice));
        $this->assertTrue($schedule->deferredAccount->is($deferred));
        $this->assertTrue($schedule->revenueAccount->is($revenue));
        $this->assertTrue($schedule->entries->first()->is($entry));
        $this->assertFalse($entry->recognized);
    }
}

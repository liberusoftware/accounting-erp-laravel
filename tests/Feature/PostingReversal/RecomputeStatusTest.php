<?php

declare(strict_types=1);

namespace Tests\Feature\PostingReversal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class RecomputeStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_recompute_reflects_posted_payments_in_both_directions(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $this->actingAs($user);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 500, 'payment_status' => 'pending']);
        // A posted payment (journal_entry_id set to any non-null id) counts; an unposted one does not.
        $entry = JournalEntry::create(['entry_date' => '2026-06-05', 'entry_type' => 'general']);
        $posted = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 500, 'payment_date' => '2026-06-05', 'team_id' => $team->id, 'journal_entry_id' => $entry->id]);

        $invoice->recomputePaymentStatus();
        $this->assertSame('paid', $invoice->fresh()->payment_status);

        // Unpost it (clear the link) and recompute — must drop back to 'pending'.
        $posted->update(['journal_entry_id' => null]);
        $invoice->recomputePaymentStatus();
        $this->assertSame('pending', $invoice->fresh()->payment_status);
    }
}

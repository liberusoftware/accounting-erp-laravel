<?php

declare(strict_types=1);

namespace Tests\Feature\PaymentPosting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_links_journal_entry_and_invoice_has_payments(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 100]);
        $payment = Payment::create([
            'invoice_id' => $invoice->id, 'payment_amount' => 60, 'payment_date' => '2026-06-05', 'team_id' => $team->id,
        ]);
        $this->actingAs($user);
        $entry = JournalEntry::create(['entry_date' => '2026-06-05', 'entry_type' => 'general']);

        $payment->update(['journal_entry_id' => $entry->id]);

        $this->assertTrue($payment->fresh()->journalEntry->is($entry));
        $this->assertTrue($invoice->payments()->first()->is($payment));
        $this->assertSame('60.00', (string) $payment->fresh()->payment_amount);
    }
}

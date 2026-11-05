<?php

declare(strict_types=1);

namespace Tests\Feature\InvoicePosting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class InvoiceJournalLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_links_to_its_journal_entry(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 100]);

        $this->actingAs($user);
        $entry = JournalEntry::create(['entry_date' => '2026-06-01', 'entry_type' => 'general']);

        $invoice->update(['journal_entry_id' => $entry->id]);

        $this->assertTrue($invoice->fresh()->journalEntry->is($entry));
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\RevenueSchedule;
use RuntimeException;

/**
 * Un-posts a posted invoice or payment: reverses its journal entry (undoing the
 * account balances via JournalEntry::reverse()), clears the source's
 * journal_entry_id so it can be re-posted, and resets derived status.
 *
 * ponytail: mutate-unpost, not an audit-trail reversing entry — the now-unposted
 * original entry lingers. Reversing recognized revenue / cascade are later slices.
 */
class PostingReversalService
{
    public function reverseInvoice(Invoice $invoice): void
    {
        if ($invoice->journal_entry_id === null) {
            throw new RuntimeException('Invoice '.((int) $invoice->getKey()).' is not posted.');
        }
        if ($invoice->payments()->whereNotNull('journal_entry_id')->exists()) {
            throw new RuntimeException('Reverse the invoice\'s posted payments before unposting the invoice.');
        }
        if (RevenueSchedule::where('invoice_id', $invoice->getKey())
            ->whereHas('entries', fn ($q) => $q->where('recognized', true))
            ->exists()) {
            throw new RuntimeException('Cannot unpost: revenue has already been recognized against this invoice.');
        }

        DB::transaction(function () use ($invoice): void {
            $entry = $invoice->journalEntry;
            if (! $entry instanceof JournalEntry) {
                throw new RuntimeException('Linked journal entry is missing.');
            }
            $entry->reverse();
            $invoice->journal_entry_id = null;
            $invoice->save();
        });
    }

    public function reversePayment(Payment $payment): void
    {
        if ($payment->journal_entry_id === null) {
            throw new RuntimeException('Payment '.((int) $payment->getKey()).' is not posted.');
        }

        DB::transaction(function () use ($payment): void {
            $entry = $payment->journalEntry;
            if (! $entry instanceof JournalEntry) {
                throw new RuntimeException('Linked journal entry is missing.');
            }
            $entry->reverse();
            $payment->journal_entry_id = null;
            $payment->save();
            $payment->invoice?->recomputePaymentStatus();
        });
    }
}

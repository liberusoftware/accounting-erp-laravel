<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\CreditNotesAndAdjustments\Enums\CreditNoteStatus;
use Liberu\Accounting\CreditNotesAndAdjustments\Exceptions\InvalidCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Models\CreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Models\CreditNoteAllocation;

final class AllocateCreditNote
{
    public function handle(CreditNote $note, string $invoiceRef, float $amount): CreditNoteAllocation
    {
        if ($note->status === CreditNoteStatus::Draft || blank($invoiceRef) || $amount <= 0 || $amount > (float) $note->amount - (float) $note->allocated_amount) {
            throw new InvalidCreditNote('Credit must be approved and allocation must fit the remaining balance.');
        }

        return DB::transaction(function () use ($note, $invoiceRef, $amount): CreditNoteAllocation {
            $allocation = $note->allocations()->create(['team_id' => $note->team_id, 'invoice_ref' => $invoiceRef, 'amount' => $amount]);
            $total = (float) $note->allocated_amount + $amount;
            $note->update(['allocated_amount' => $total, 'status' => $total >= (float) $note->amount ? CreditNoteStatus::Allocated : CreditNoteStatus::PartiallyAllocated]);

            return $allocation;
        });
    }
}

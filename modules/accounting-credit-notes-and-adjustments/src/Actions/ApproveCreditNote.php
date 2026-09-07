<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Actions;

use Illuminate\Support\Carbon;
use Liberu\Accounting\CreditNotesAndAdjustments\Enums\CreditNoteStatus;
use Liberu\Accounting\CreditNotesAndAdjustments\Exceptions\InvalidCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Models\CreditNote;

final class ApproveCreditNote
{
    public function handle(CreditNote $note, int $actor): CreditNote
    {
        if ($note->status !== CreditNoteStatus::Draft) {
            throw new InvalidCreditNote('Only draft credits can be approved.');
        } $note->update(['status' => CreditNoteStatus::Approved, 'approved_by' => $actor, 'approved_at' => Carbon::now()]);

        return $note->fresh('allocations');
    }
}

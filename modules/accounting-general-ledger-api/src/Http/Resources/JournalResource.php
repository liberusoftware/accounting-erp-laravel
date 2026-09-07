<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;

/** @mixin JournalEntry */
final class JournalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $journal = $this->resource;

        return ['id' => (string) $journal->getKey(), 'type' => 'accounting-journal', 'attributes' => ['book_id' => $journal->book_id, 'entry_number' => $journal->entry_number, 'entry_date' => $journal->entry_date?->toDateString(), 'journal_type' => $journal->journal_type?->value, 'status' => $journal->status?->value, 'description' => $journal->description, 'lines' => $journal->lines->map(fn ($line) => ['account_id' => $line->account_id, 'debit' => (string) $line->debit, 'credit' => (string) $line->credit, 'description' => $line->description])->values()]];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class ArchiveEInvoice
{
    public function handle(EInvoiceDocument $d, ?string $actor = null): EInvoiceDocument
    {
        if (! in_array($d->status, [DocumentStatus::Accepted, DocumentStatus::Rejected, DocumentStatus::Reconciled], true)) {
            throw new InvalidEInvoice('Only completed documents can be archived.');
        }$d->update(['status' => DocumentStatus::Archived, 'archived_at' => now()]);
        $d->events()->create(['event' => 'archived', 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new EInvoiceStatusChanged($d, 'archived', $actor));

        return $d;
    }
}

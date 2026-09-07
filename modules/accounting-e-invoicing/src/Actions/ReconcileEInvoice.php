<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class ReconcileEInvoice
{
    public function handle(EInvoiceDocument $d, ?string $actor = null): EInvoiceDocument
    {
        if ($d->status !== DocumentStatus::Accepted) {
            throw new InvalidEInvoice('Only accepted documents can be reconciled.');
        }$d->update(['status' => DocumentStatus::Reconciled]);
        $d->events()->create(['event' => 'reconciled', 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new EInvoiceStatusChanged($d, 'reconciled', $actor));

        return $d;
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class ValidateEInvoice
{
    public function handle(EInvoiceDocument $d, ?string $actor = null): EInvoiceDocument
    {
        if ($d->status !== DocumentStatus::Draft) {
            throw new InvalidEInvoice('Only draft documents can be validated.');
        }$payload = $d->payload;
        if (blank($payload['invoice_number'] ?? null) || blank($payload['lines'] ?? null) || ! is_array($payload['lines'])) {
            throw new InvalidEInvoice('Structured invoices require an invoice number and line items.');
        }$d->update(['status' => DocumentStatus::Validated]);
        $d->events()->create(['event' => 'validated', 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new EInvoiceStatusChanged($d, 'validated', $actor));

        return $d;
    }
}

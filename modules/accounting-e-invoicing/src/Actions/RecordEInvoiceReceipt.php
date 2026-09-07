<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class RecordEInvoiceReceipt
{
    public function handle(EInvoiceDocument $d, bool $accepted, ?string $message = null, ?string $actor = null): EInvoiceDocument
    {
        if ($d->status !== DocumentStatus::Submitted) {
            throw new InvalidEInvoice('Only submitted documents can receive a provider receipt.');
        }$event = $accepted ? 'accepted' : 'rejected';
        if (! $accepted && blank($message)) {
            throw new InvalidEInvoice('A rejection receipt requires a message.');
        }$d->update(['status' => $accepted ? DocumentStatus::Accepted : DocumentStatus::Rejected, 'received_at' => now()]);
        $d->events()->create(['event' => $event, 'provider_ref' => $d->provider_ref, 'actor_ref' => $actor, 'message' => $message]);
        $d = $d->refresh();
        event(new EInvoiceStatusChanged($d, $event, $actor));

        return $d;
    }
}

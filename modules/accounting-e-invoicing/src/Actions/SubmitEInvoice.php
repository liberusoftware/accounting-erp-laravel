<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class SubmitEInvoice
{
    public function handle(EInvoiceDocument $d, string $provider, ?string $actor = null): EInvoiceDocument
    {
        if ($d->status !== DocumentStatus::Signed || blank($provider)) {
            throw new InvalidEInvoice('Only signed documents can be routed to a provider.');
        }$d->update(['status' => DocumentStatus::Submitted, 'provider_ref' => $provider, 'submitted_at' => now()]);
        $d->events()->create(['event' => 'submitted', 'provider_ref' => $provider, 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new EInvoiceStatusChanged($d, 'submitted', $actor));

        return $d;
    }
}

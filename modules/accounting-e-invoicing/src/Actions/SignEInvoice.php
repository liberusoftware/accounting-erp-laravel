<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class SignEInvoice
{
    public function handle(EInvoiceDocument $d, string $signature, ?string $actor = null): EInvoiceDocument
    {
        if ($d->status !== DocumentStatus::Validated || blank($signature)) {
            throw new InvalidEInvoice('Only validated documents can be signed with a non-empty signature.');
        }$d->update(['status' => DocumentStatus::Signed, 'signature' => $signature]);
        $d->events()->create(['event' => 'signed', 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new EInvoiceStatusChanged($d, 'signed', $actor));

        return $d;
    }
}

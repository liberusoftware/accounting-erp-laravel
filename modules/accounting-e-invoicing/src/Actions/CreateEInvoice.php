<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class CreateEInvoice
{
    public function handle(array $a): EInvoiceDocument
    {
        foreach (['legal_entity_id', 'document_ref', 'document_type', 'format', 'tax_id', 'counterparty_ref', 'currency', 'payload'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidEInvoice("Missing document field [{$k}].");
            }
        }if (! in_array($a['format'], ['ubl', 'factur-x', 'peppol'], true) || ! preg_match('/^[A-Za-z]{3}$/', (string) $a['currency']) || ! is_array($a['payload'])) {
            throw new InvalidEInvoice('The structured format, currency, or payload is invalid.');
        }

        return DB::transaction(function () use ($a): EInvoiceDocument {
            $d = EInvoiceDocument::create(['legal_entity_id' => $a['legal_entity_id'], 'document_ref' => $a['document_ref'], 'document_type' => $a['document_type'], 'format' => $a['format'], 'status' => DocumentStatus::Draft, 'tax_id' => $a['tax_id'], 'counterparty_ref' => $a['counterparty_ref'], 'currency' => strtoupper($a['currency']), 'payload' => $a['payload'], 'metadata' => $a['metadata'] ?? null]);
            $d->events()->create(['event' => 'created', 'actor_ref' => $a['actor_ref'] ?? null]);

            return $d->refresh();
        });
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

/** @mixin EInvoiceDocument */ final class EInvoiceResource extends JsonResource
{
    public function toArray(Request $r): array
    {
        $d = $this->resource;

        return ['id' => $d->getKey(), 'document_ref' => $d->document_ref, 'document_type' => $d->document_type, 'format' => $d->format, 'status' => $d->status->value, 'tax_id' => $d->tax_id, 'counterparty_ref' => $d->counterparty_ref, 'currency' => $d->currency, 'provider_ref' => $d->provider_ref, 'payload' => $d->payload, 'signature' => $d->signature, 'submitted_at' => $d->submitted_at, 'received_at' => $d->received_at, 'archived_at' => $d->archived_at, 'events' => $this->whenLoaded('events')];
    }
}

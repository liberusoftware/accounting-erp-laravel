<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

/** @mixin SalesInvoice */
final class SalesInvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-sales-invoice', 'attributes' => ['invoice_number' => $this->invoice_number, 'party_id' => $this->party_id, 'invoice_date' => $this->invoice_date->toDateString(), 'due_on' => $this->due_on?->toDateString(), 'status' => $this->status->value, 'subtotal' => (string) $this->subtotal, 'discount_total' => (string) $this->discount_total, 'tax_total' => (string) $this->tax_total, 'total' => (string) $this->total, 'currency' => $this->currency, 'delivery_status' => $this->delivery_status, 'lines' => $this->whenLoaded('lines', fn () => $this->lines->map(fn ($line) => ['description' => $line->description, 'quantity' => (string) $line->quantity, 'unit_price' => (string) $line->unit_price, 'discount_rate' => (string) $line->discount_rate, 'tax_rate' => (string) $line->tax_rate, 'net_amount' => (string) $line->net_amount, 'tax_amount' => (string) $line->tax_amount])->values())]];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBillCredit;
use Liberu\Accounting\SupplierBills\Models\SupplierBillDocument;
use Liberu\Accounting\SupplierBills\Models\SupplierBillLine;
use Liberu\Accounting\SupplierBills\Models\SupplierBillMatch;

/** @mixin SupplierBill */
final class SupplierBillResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-supplier-bills', 'attributes' => [
            'party_id' => $this->party_id, 'bill_number' => $this->bill_number, 'bill_date' => $this->bill_date?->toDateString(), 'due_on' => $this->due_on?->toDateString(),
            'status' => $this->status->value, 'payment_status' => $this->payment_status->value, 'subtotal' => (float) $this->subtotal, 'tax_total' => (float) $this->tax_total, 'total' => (float) $this->total, 'amount_paid' => (float) $this->amount_paid, 'outstanding' => (float) $this->outstanding(), 'currency' => $this->currency, 'capture_source' => $this->capture_source, 'purchase_order_reference' => $this->purchase_order_reference, 'reference_number' => $this->reference_number, 'approval_status' => $this->approval_status, 'rejection_reason' => $this->rejection_reason, 'recurring' => $this->recurring, 'external_ids' => $this->external_ids, 'lines' => $this->when($this->relationLoaded('lines'), fn (): array => $this->lines->map(fn (SupplierBillLine $line): array => ['id' => (string) $line->id, 'account_code' => $line->account_code, 'description' => $line->description, 'quantity' => (float) $line->quantity, 'unit_price' => (float) $line->unit_price, 'discount_rate' => (float) $line->discount_rate, 'tax_rate' => (float) $line->tax_rate, 'net_amount' => (float) $line->net_amount, 'tax_amount' => (float) $line->tax_amount])->values()->all()), 'credits' => $this->when($this->relationLoaded('credits'), fn (): array => $this->credits->map(fn (SupplierBillCredit $credit): array => ['id' => (string) $credit->id, 'amount' => (float) $credit->amount, 'currency' => $credit->currency, 'reason' => $credit->reason, 'reference' => $credit->reference])->values()->all()), 'documents' => $this->when($this->relationLoaded('documents'), fn (): array => $this->documents->map(fn (SupplierBillDocument $document): array => ['id' => (string) $document->id, 'original_name' => $document->original_name, 'mime_type' => $document->mime_type])->values()->all()), 'matches' => $this->when($this->relationLoaded('matches'), fn (): array => $this->matches->map(fn (SupplierBillMatch $match): array => ['id' => (string) $match->id, 'match_type' => $match->match_type, 'matched_type' => $match->matched_type, 'matched_id' => (string) $match->matched_id, 'quantity' => $match->quantity === null ? null : (float) $match->quantity, 'amount' => $match->amount === null ? null : (float) $match->amount, 'status' => $match->status])->values()->all()),
        ], 'meta' => ['overdue' => $this->isOverdue()], 'links' => ['self' => url('/api/v1/accounting/supplier-bills/'.$this->id)]];
    }
}

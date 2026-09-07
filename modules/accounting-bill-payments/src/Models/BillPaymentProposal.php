<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;

final class BillPaymentProposal extends Model
{
    protected $table = 'accounting_bill_payment_proposals';

    protected $fillable = ['team_id', 'supplier_id', 'bill_reference', 'amount', 'currency', 'due_date', 'discount_date', 'discount_rate', 'payment_date', 'bank_details', 'provider', 'provider_connection_id', 'payment_payload', 'idempotency_key', 'status', 'requested_by', 'approved_by', 'approved_at', 'submitted_at', 'paid_at', 'rejected_at', 'failure_code', 'failure_message', 'provider_reference', 'provider_result', 'remittance_reference', 'remittance_sent_at', 'metadata'];

    protected $hidden = ['bank_details', 'payment_payload', 'provider_result'];

    protected $casts = ['amount' => 'decimal:2', 'due_date' => 'date', 'discount_date' => 'date', 'payment_date' => 'date', 'discount_rate' => 'decimal:4', 'status' => BillPaymentStatus::class, 'bank_details' => 'encrypted:array', 'payment_payload' => 'encrypted:array', 'approved_at' => 'datetime', 'submitted_at' => 'datetime', 'paid_at' => 'datetime', 'rejected_at' => 'datetime', 'provider_result' => 'encrypted:array', 'remittance_sent_at' => 'datetime', 'metadata' => 'array'];

    protected $attributes = ['status' => 'draft'];

    public function approvalAmount(): float
    {
        return (float) $this->amount;
    }
}

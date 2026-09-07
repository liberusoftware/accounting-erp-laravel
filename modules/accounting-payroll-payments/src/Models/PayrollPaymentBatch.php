<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPayments\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;

/**
 * @property int $id
 * @property PaymentStatus $status
 * @property float|string $net_pay_amount
 * @property float|string $liability_amount
 */
final class PayrollPaymentBatch extends Model
{
    protected $table = 'accounting_payroll_payment_batches';

    protected $fillable = ['team_id', 'batch_ref', 'net_pay_ref', 'liability_ref', 'currency', 'net_pay_amount', 'liability_amount', 'status', 'provider', 'provider_payment_ref', 'failure_code', 'failure_message', 'approved_by', 'approved_at', 'submitted_at', 'settled_at', 'reconciled_at', 'reconciliation_ref', 'metadata'];

    protected $casts = ['net_pay_amount' => 'decimal:2', 'liability_amount' => 'decimal:2', 'status' => PaymentStatus::class, 'approved_at' => 'datetime', 'submitted_at' => 'datetime', 'settled_at' => 'datetime', 'reconciled_at' => 'datetime', 'metadata' => 'array'];

    public function totalAmount(): float
    {
        return (float) $this->net_pay_amount + (float) $this->liability_amount;
    }
}

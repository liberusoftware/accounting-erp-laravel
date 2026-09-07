<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilities\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\PayrollLiabilities\Enums\LiabilityStatus;

/**
 * @property int $id
 * @property LiabilityStatus $status
 * @property float|string $amount
 * @property float|string $paid_amount
 * @property CarbonInterface|null $due_on
 */
final class PayrollLiability extends Model
{
    protected $table = 'accounting_payroll_liabilities';

    protected $fillable = ['team_id', 'agency_ref', 'payee_ref', 'liability_ref', 'currency', 'amount', 'paid_amount', 'due_on', 'status', 'payment_ref', 'allocation_ref', 'exception_code', 'exception_message', 'reconciliation_ref', 'reconciled_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'due_on' => 'date', 'status' => LiabilityStatus::class, 'reconciled_at' => 'datetime', 'metadata' => 'array'];

    public function outstanding(): float
    {
        return max(0.0, (float) $this->amount - (float) $this->paid_amount);
    }
}

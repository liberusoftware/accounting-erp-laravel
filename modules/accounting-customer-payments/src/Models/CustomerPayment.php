<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentKind;
use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentStatus;

final class CustomerPayment extends Model
{
    protected $table = 'accounting_customer_payments';

    protected $fillable = ['team_id', 'customer_id', 'kind', 'reference', 'status', 'currency', 'amount', 'allocated_amount', 'refunded_amount', 'gateway_reference', 'deposit_reference', 'metadata'];

    protected $casts = ['kind' => CustomerPaymentKind::class, 'status' => CustomerPaymentStatus::class, 'amount' => 'decimal:8', 'allocated_amount' => 'decimal:8', 'refunded_amount' => 'decimal:8', 'metadata' => 'array'];

    public function allocations(): HasMany
    {
        return $this->hasMany(CustomerPaymentAllocation::class, 'payment_id');
    }
}

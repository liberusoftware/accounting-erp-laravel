<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CustomerPaymentAllocation extends Model
{
    protected $table = 'accounting_customer_payment_allocations';

    protected $fillable = ['payment_id', 'team_id', 'document_ref', 'amount'];

    protected $casts = ['amount' => 'decimal:8'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(CustomerPayment::class, 'payment_id');
    }
}

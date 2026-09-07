<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property float|string $amount
 * @property string $currency
 * @property string $reason
 * @property string|null $reference
 */
final class SupplierBillCredit extends Model
{
    protected $table = 'accounting_supplier_bill_credits';

    protected $fillable = ['bill_id', 'amount', 'currency', 'reason', 'reference', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'metadata' => 'array'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }
}

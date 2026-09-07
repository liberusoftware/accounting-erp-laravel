<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $match_type
 * @property string $matched_type
 * @property string $matched_id
 * @property float|string|null $quantity
 * @property float|string|null $amount
 * @property string $status
 */
final class SupplierBillMatch extends Model
{
    protected $table = 'accounting_supplier_bill_matches';

    protected $fillable = ['bill_id', 'match_type', 'matched_type', 'matched_id', 'quantity', 'amount', 'status', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'amount' => 'decimal:2', 'metadata' => 'array'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $account_code
 * @property string $description
 * @property float|string $quantity
 * @property float|string $unit_price
 * @property float|string $discount_rate
 * @property float|string $tax_rate
 * @property float|string $net_amount
 * @property float|string $tax_amount
 */
final class SupplierBillLine extends Model
{
    protected $table = 'accounting_supplier_bill_lines';

    protected $fillable = ['bill_id', 'account_code', 'description', 'quantity', 'unit_price', 'discount_rate', 'tax_rate', 'net_amount', 'tax_amount', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'unit_price' => 'decimal:4', 'discount_rate' => 'decimal:4', 'tax_rate' => 'decimal:4', 'net_amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'metadata' => 'array'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }
}

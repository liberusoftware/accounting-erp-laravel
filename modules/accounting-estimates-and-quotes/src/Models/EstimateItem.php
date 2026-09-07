<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property string $description @property string $quantity @property string $unit_price @property string $amount */
final class EstimateItem extends Model
{
    protected $table = 'accounting_sales_estimate_items';

    protected $fillable = ['estimate_id', 'item_ref', 'description', 'quantity', 'unit_price', 'tax_rate', 'amount', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'unit_price' => 'decimal:2', 'tax_rate' => 'decimal:4', 'amount' => 'decimal:2', 'metadata' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }
}

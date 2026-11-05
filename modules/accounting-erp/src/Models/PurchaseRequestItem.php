<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|null $description
 * @property int|float|string $quantity
 * @property int|float|string $unit_price
 * @property int|float|string $total_price
 */
class PurchaseRequestItem extends Model
{
    use HasFactory;

    #[\Override]
    protected $fillable = [
        'purchase_request_id', 'description', 'quantity', 'unit_price', 'total_price',
    ];

    #[\Override]
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    #[\Override]
    protected static function boot(): void
    {
        parent::boot();

        // The create form captures quantity + unit_price but not the line total, so
        // derive it when absent — otherwise items save with total_price = 0 and that
        // zero propagates into the converted purchase order's line items.
        static::saving(function (PurchaseRequestItem $item): void {
            if (empty($item->total_price)) {
                $item->total_price = (float) $item->quantity * (float) $item->unit_price;
            }
        });
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
}

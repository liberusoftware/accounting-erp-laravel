<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxRecordType;
use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxStatus;

/**
 * @property int $id
 * @property string $context_id
 * @property SalesTaxRecordType $type
 * @property string $jurisdiction
 * @property string|null $origin
 * @property string|null $destination
 * @property float|string $rate
 * @property float|string $taxable_base
 * @property float|string $liability
 * @property SalesTaxStatus $status
 * @property string $period_start
 * @property string $period_end
 * @property array<string,mixed>|null $metadata
 */
final class SalesTaxRecord extends Model
{
    protected $table = 'accounting_sales_tax_records';

    protected $fillable = ['context_id', 'type', 'jurisdiction', 'origin', 'destination', 'rate', 'taxable_base', 'liability', 'status', 'period_start', 'period_end', 'metadata'];

    protected $casts = ['type' => SalesTaxRecordType::class, 'status' => SalesTaxStatus::class, 'rate' => 'decimal:6', 'taxable_base' => 'decimal:2', 'liability' => 'decimal:2', 'metadata' => 'array'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SalesTaxStatus::Active->value);
    }
}

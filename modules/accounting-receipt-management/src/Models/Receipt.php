<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\ReceiptManagement\Enums\ReceiptStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $file_ref
 * @property ReceiptStatus $status
 * @property string|null $merchant
 * @property float|string|null $amount
 * @property string|null $currency
 * @property string|null $retention_until
 * @property array<string,mixed>|null $metadata
 */
final class Receipt extends Model
{
    protected $table = 'accounting_receipts';

    protected $fillable = ['team_id', 'file_ref', 'source_type', 'source_id', 'merchant', 'amount', 'currency', 'receipt_date', 'status', 'retention_until', 'metadata'];

    protected $casts = ['status' => ReceiptStatus::class, 'amount' => 'decimal:2', 'receipt_date' => 'date', 'retention_until' => 'date', 'metadata' => 'array'];

    public function matches(): HasMany
    {
        return $this->hasMany(ReceiptMatch::class, 'receipt_id');
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(ReceiptAnnotation::class, 'receipt_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $receipt_id
 * @property string $target_type
 * @property string $target_id
 * @property string $status
 * @property float|string|null $matched_amount
 */
final class ReceiptMatch extends Model
{
    protected $table = 'accounting_receipt_matches';

    protected $fillable = ['receipt_id', 'target_type', 'target_id', 'matched_amount', 'status', 'confidence', 'metadata'];

    protected $casts = ['matched_amount' => 'decimal:2', 'confidence' => 'decimal:4', 'metadata' => 'array'];
}

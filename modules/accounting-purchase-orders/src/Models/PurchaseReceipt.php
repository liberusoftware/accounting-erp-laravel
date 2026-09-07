<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $order_id @property string $receipt_ref @property array<string,mixed> $lines @property string $status */
final class PurchaseReceipt extends Model
{
    protected $table = 'accounting_purchase_order_receipts';

    protected $fillable = ['order_id', 'receipt_ref', 'received_on', 'lines', 'status', 'document_ref', 'metadata'];

    protected $casts = ['received_on' => 'date', 'lines' => 'array', 'metadata' => 'array'];
}

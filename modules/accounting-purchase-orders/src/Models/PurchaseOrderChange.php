<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $order_id @property int $version @property array<string,mixed> $changes @property string $reason */
final class PurchaseOrderChange extends Model
{
    protected $table = 'accounting_purchase_order_changes';

    protected $fillable = ['order_id', 'version', 'changes', 'reason', 'actor_ref', 'approved_at', 'metadata'];

    protected $casts = ['changes' => 'array', 'approved_at' => 'datetime', 'metadata' => 'array'];
}

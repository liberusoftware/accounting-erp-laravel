<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\InventoryAccounting\Models\InventoryReconciliation;

final class InventoryReconciled
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly InventoryReconciliation $reconciliation) {}
}

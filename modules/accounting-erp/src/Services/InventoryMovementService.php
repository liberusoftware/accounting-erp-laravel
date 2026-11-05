<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\InventoryCostLayer;
use Liberu\Accounting\Erp\Models\InventoryItem;
use Liberu\Accounting\Erp\Models\InventoryTransaction;
use Liberu\Accounting\Erp\Models\JournalEntry;

/**
 * Records inventory stock movements from the UI: purchases add a cost layer and
 * stock; sales consume layers (FIFO/LIFO/average), reduce stock, and post COGS.
 */
class InventoryMovementService
{
    public function __construct(
        private readonly InventoryValuationService $valuation,
        private readonly InventoryPostingService $posting,
    ) {}

    /**
     * Record a purchase: new cost layer, weighted-average update, stock increase.
     */
    public function recordPurchase(InventoryItem $item, int $quantity, float $unitCost): InventoryCostLayer
    {
        $layer = InventoryCostLayer::create([
            'inventory_item_id' => $item->id,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'purchase_date' => now(),
        ]);

        // Weighted-average uses the pre-purchase quantity, so update it before the stock bump.
        $this->valuation->updateAverageCost($item, $quantity, $unitCost);
        $item->updateQuantity($quantity);

        return $layer;
    }

    /**
     * Record a sale: compute COGS from cost layers, reduce stock, post the COGS entry.
     *
     * @return array{cogs: float, journal_entry: JournalEntry}
     */
    public function recordSale(InventoryItem $item, int $quantity, Account $cogsExpense): array
    {
        // A transient transaction drives the valuation calc (consumes the layers).
        $sale = new InventoryTransaction(['inventory_item_id' => $item->id, 'quantity' => $quantity]);
        $cogs = (float) $this->valuation->calculateCostOfGoodsSold($sale);

        $item->updateQuantity(-$quantity);

        $entry = $this->posting->postCogs($item, $cogs, $cogsExpense);

        return ['cogs' => $cogs, 'journal_entry' => $entry];
    }
}

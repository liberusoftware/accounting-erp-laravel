<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Actions;

use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\EstimateItem;

final class AddEstimateItem
{
    public function handle(Estimate $e, array $a): EstimateItem
    {
        if ($e->status !== EstimateStatus::Draft) {
            throw new InvalidEstimate('Only draft estimates can be edited.');
        }foreach (['description', 'quantity', 'unit_price'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidEstimate("Missing item field [{$k}].");
            }
        }$quantity = (float) $a['quantity'];
        $price = (float) $a['unit_price'];
        if ($quantity <= 0 || $price < 0) {
            throw new InvalidEstimate('Item quantity must be positive and price cannot be negative.');
        }

        return $e->items()->create(['item_ref' => $a['item_ref'] ?? null, 'description' => $a['description'], 'quantity' => $quantity, 'unit_price' => $price, 'tax_rate' => $a['tax_rate'] ?? 0, 'amount' => round($quantity * $price, 2), 'metadata' => $a['metadata'] ?? null]);
    }
}

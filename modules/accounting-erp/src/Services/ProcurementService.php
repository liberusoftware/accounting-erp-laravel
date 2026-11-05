<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\PurchaseOrder;
use Liberu\Accounting\Erp\Models\PurchaseRequest;

class ProcurementService
{
    public function createPurchaseOrderFromRequest(PurchaseRequest $request): PurchaseOrder
    {
        if ($request->approval_status !== 'approved') {
            throw new \DomainException('Only an approved purchase request can become a purchase order.');
        }
        if ($request->purchaseOrder()->exists()) {
            throw new \DomainException('This request already has a purchase order.');
        }

        return DB::transaction(function () use ($request): PurchaseOrder {
            $po = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'purchase_request_id' => $request->id,
                'po_number' => PurchaseOrder::generatePoNumber(),
                'order_date' => today(),
                'status' => 'draft',
                'total_amount' => $request->total_amount,
                'team_id' => $request->team_id,
            ]);

            foreach ($request->items as $item) {
                $po->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            return $po;
        });
    }
}

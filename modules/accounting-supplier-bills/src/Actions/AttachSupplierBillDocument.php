<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBillDocument;

final class AttachSupplierBillDocument
{
    public function handle(SupplierBill $bill, array $attributes): SupplierBillDocument
    {
        return DB::transaction(function () use ($bill, $attributes): SupplierBillDocument {
            /** @var SupplierBillDocument $document */
            $document = $bill->documents()->firstOrCreate(['sha256' => $attributes['sha256']], $attributes);

            return $document;
        });
    }
}

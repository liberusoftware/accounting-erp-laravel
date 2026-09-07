<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\SupplierBills\Actions\CreateSupplierBill as CreateSupplierBillAction;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;
use Liberu\Accounting\SupplierBillsFilament\Resources\SupplierBillResource;

final class CreateSupplierBill extends CreateRecord
{
    protected static string $resource = SupplierBillResource::class;

    protected function handleRecordCreation(array $data): SupplierBill
    {
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        return app(CreateSupplierBillAction::class)->handle($data, $lines);
    }
}

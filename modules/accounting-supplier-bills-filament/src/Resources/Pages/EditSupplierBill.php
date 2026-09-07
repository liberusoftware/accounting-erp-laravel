<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\SupplierBills\Actions\UpdateSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;
use Liberu\Accounting\SupplierBillsFilament\Resources\SupplierBillResource;

final class EditSupplierBill extends EditRecord
{
    protected static string $resource = SupplierBillResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof SupplierBill) {
            throw new \LogicException('Supplier bill resource received an invalid record.');
        }
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        return app(UpdateSupplierBill::class)->handle($record, $data, $lines);
    }
}

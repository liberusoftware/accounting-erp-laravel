<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxRecordType;
use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxStatus;
use Liberu\Accounting\SalesTaxAndGst\Exceptions\InvalidSalesTax;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;

final class CreateSalesTaxRecord
{
    public function handle(array $attributes): SalesTaxRecord
    {
        return DB::transaction(function () use ($attributes): SalesTaxRecord {
            foreach (['context_id', 'type', 'jurisdiction', 'period_start', 'period_end'] as $key) {
                if (blank($attributes[$key] ?? null)) {
                    throw new InvalidSalesTax("Sales tax field [{$key}] is required.");
                }
            }$type = $attributes['type'] instanceof SalesTaxRecordType ? $attributes['type']->value : $attributes['type'];
            if (! SalesTaxRecordType::tryFrom((string) $type)) {
                throw new InvalidSalesTax('Unknown sales tax record type.');
            }if ((float) ($attributes['rate'] ?? 0) < 0 || (float) ($attributes['rate'] ?? 0) > 100) {
                throw new InvalidSalesTax('Tax rate must be between 0 and 100.');
            }if ((float) ($attributes['taxable_base'] ?? 0) < 0) {
                throw new InvalidSalesTax('Taxable base must not be negative.');
            }

            return SalesTaxRecord::create(array_merge($attributes, ['type' => $type, 'status' => $attributes['status'] ?? SalesTaxStatus::Draft, 'liability' => $attributes['liability'] ?? round((float) ($attributes['taxable_base'] ?? 0) * (float) ($attributes['rate'] ?? 0) / 100, 2)]));
        });
    }
}

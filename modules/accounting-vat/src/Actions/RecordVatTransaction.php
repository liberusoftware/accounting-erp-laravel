<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Vat\Enums\VatDirection;
use Liberu\Accounting\Vat\Enums\VatRecordStatus;
use Liberu\Accounting\Vat\Exceptions\InvalidVat;
use Liberu\Accounting\Vat\Models\VatRecord;

final class RecordVatTransaction
{
    public function handle(array $attributes): VatRecord
    {
        $directions = array_map(static fn (VatDirection $direction): string => $direction->value, VatDirection::cases());
        $direction = $attributes['direction'] instanceof VatDirection ? $attributes['direction']->value : $attributes['direction'] ?? null;

        if (! in_array($direction, $directions, true) || blank($attributes['tax_code'] ?? null) || blank($attributes['occurred_on'] ?? null)) {
            throw new InvalidVat('A VAT direction, tax code, and transaction date are required.');
        }
        if ((float) ($attributes['net_amount'] ?? 0) < 0 || (float) ($attributes['tax_amount'] ?? 0) < 0 || (float) ($attributes['tax_rate'] ?? 0) < 0) {
            throw new InvalidVat('VAT amounts and rates cannot be negative.');
        }

        return DB::transaction(fn (): VatRecord => VatRecord::create(array_merge($attributes, ['direction' => $direction, 'status' => $attributes['status'] ?? VatRecordStatus::Draft, 'scheme' => $attributes['scheme'] ?? 'standard'])));
    }
}

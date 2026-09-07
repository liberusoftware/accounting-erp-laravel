<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Vat\Enums\VatReturnStatus;
use Liberu\Accounting\Vat\Exceptions\InvalidVat;
use Liberu\Accounting\Vat\Models\VatReturn;

final class CreateVatReturn
{
    public function handle(array $attributes): VatReturn
    {
        if (blank($attributes['team_id'] ?? null) || blank($attributes['period_start'] ?? null) || blank($attributes['period_end'] ?? null)) {
            throw new InvalidVat('A team and VAT return period are required.');
        }
        if ($attributes['period_start'] > $attributes['period_end']) {
            throw new InvalidVat('A VAT return period must be chronological.');
        }

        return DB::transaction(fn (): VatReturn => VatReturn::create(array_merge($attributes, ['scheme' => $attributes['scheme'] ?? 'standard', 'status' => VatReturnStatus::Draft, 'boxes' => $attributes['boxes'] ?? []])));
    }
}

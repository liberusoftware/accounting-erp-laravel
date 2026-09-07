<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Actions;

use Liberu\Accounting\Vat\Exceptions\InvalidVat;
use Liberu\Accounting\Vat\Models\VatAdjustment;
use Liberu\Accounting\Vat\Models\VatReturn;

final class AddVatAdjustment
{
    public function handle(VatReturn $vatReturn, array $attributes): VatAdjustment
    {
        if (! isset($attributes['box']) || blank($attributes['reason'] ?? null) || ! isset($attributes['amount'])) {
            throw new InvalidVat('A VAT box, adjustment amount, and reason are required.');
        }

        return $vatReturn->adjustments()->create($attributes);
    }
}

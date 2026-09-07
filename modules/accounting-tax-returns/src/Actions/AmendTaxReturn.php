<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturns\Actions;

use Liberu\Accounting\TaxReturns\Enums\TaxReturnStatus;
use Liberu\Accounting\TaxReturns\Exceptions\InvalidTaxReturn;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;

final class AmendTaxReturn
{
    public function handle(TaxReturn $taxReturn): TaxReturn
    {
        if ($taxReturn->status !== TaxReturnStatus::Accepted) {
            throw new InvalidTaxReturn('Only accepted returns can be amended.');
        }
        $taxReturn->update(['status' => TaxReturnStatus::Amended]);

        return $taxReturn->refresh();
    }
}

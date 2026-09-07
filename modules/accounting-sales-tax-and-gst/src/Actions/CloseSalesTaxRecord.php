<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst\Actions;

use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxStatus;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;

final class CloseSalesTaxRecord
{
    public function handle(SalesTaxRecord $record): SalesTaxRecord
    {
        $record->update(['status' => SalesTaxStatus::Closed]);

        return $record->refresh();
    }
}

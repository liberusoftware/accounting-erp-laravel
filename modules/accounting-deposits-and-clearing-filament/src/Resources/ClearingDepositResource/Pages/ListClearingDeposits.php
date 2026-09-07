<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingFilament\Resources\ClearingDepositResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\DepositsAndClearingFilament\Resources\ClearingDepositResource;

final class ListClearingDeposits extends ListRecords
{
    protected static string $resource = ClearingDepositResource::class;
}

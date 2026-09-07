<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansFilament\Resources\DebtFacilityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\DebtAndLoansFilament\Resources\DebtFacilityResource;

final class ListDebtFacilities extends ListRecords
{
    protected static string $resource = DebtFacilityResource::class;
}

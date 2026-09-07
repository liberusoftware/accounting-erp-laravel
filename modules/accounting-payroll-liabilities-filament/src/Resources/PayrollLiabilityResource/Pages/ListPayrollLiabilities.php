<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesFilament\Resources\PayrollLiabilityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PayrollLiabilitiesFilament\Resources\PayrollLiabilityResource;

final class ListPayrollLiabilities extends ListRecords
{
    protected static string $resource = PayrollLiabilityResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationFilament\Resources\PayrollImportResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PayrollIntegrationFilament\Resources\PayrollImportResource;

final class ListPayrollImports extends ListRecords
{
    protected static string $resource = PayrollImportResource::class;
}

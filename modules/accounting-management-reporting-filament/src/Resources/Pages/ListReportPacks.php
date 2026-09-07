<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ManagementReportingFilament\Resources\ReportPackResource;

final class ListReportPacks extends ListRecords
{
    protected static string $resource = ReportPackResource::class;
}

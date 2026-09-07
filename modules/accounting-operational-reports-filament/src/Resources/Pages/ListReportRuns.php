<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\OperationalReportsFilament\Resources\ReportRunResource;

final class ListReportRuns extends ListRecords
{
    protected static string $resource = ReportRunResource::class;
}

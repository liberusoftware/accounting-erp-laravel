<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\BusinessInsightsFilament\Resources\InsightSnapshotResource;

final class ListInsightSnapshots extends ListRecords
{
    protected static string $resource = InsightSnapshotResource::class;
}

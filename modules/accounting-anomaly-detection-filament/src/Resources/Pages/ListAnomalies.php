<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AnomalyDetectionFilament\Resources\AnomalyResource;

final class ListAnomalies extends ListRecords
{
    protected static string $resource = AnomalyResource::class;
}

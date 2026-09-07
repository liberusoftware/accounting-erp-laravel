<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\JobEstimatesFilament\Resources\JobEstimateResource;

final class ListJobEstimates extends ListRecords
{
    protected static string $resource = JobEstimateResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ProjectsAndJobsFilament\Resources\ProjectJobResource;

final class ListProjectJobs extends ListRecords
{
    protected static string $resource = ProjectJobResource::class;
}

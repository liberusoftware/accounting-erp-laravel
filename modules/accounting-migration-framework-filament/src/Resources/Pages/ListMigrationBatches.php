<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\MigrationFrameworkFilament\Resources\MigrationBatchResource;

final class ListMigrationBatches extends ListRecords
{
    protected static string $resource = MigrationBatchResource::class;
}

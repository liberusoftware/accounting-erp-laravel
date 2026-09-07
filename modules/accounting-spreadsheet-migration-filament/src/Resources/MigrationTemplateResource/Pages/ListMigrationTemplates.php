<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationFilament\Resources\MigrationTemplateResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\SpreadsheetMigrationFilament\Resources\MigrationTemplateResource;

final class ListMigrationTemplates extends ListRecords
{
    protected static string $resource = MigrationTemplateResource::class;
}

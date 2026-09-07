<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\XeroMigrationFilament\Resources\XeroConnectionResource;

final class ListXeroConnections extends ListRecords
{
    protected static string $resource = XeroConnectionResource::class;
}

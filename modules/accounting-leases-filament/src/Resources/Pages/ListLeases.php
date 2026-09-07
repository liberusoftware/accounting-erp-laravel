<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\LeasesFilament\Resources\LeaseResource;

final class ListLeases extends ListRecords
{
    protected static string $resource = LeaseResource::class;
}

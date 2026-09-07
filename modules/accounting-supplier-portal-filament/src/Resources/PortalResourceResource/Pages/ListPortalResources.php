<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalFilament\Resources\PortalResourceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\SupplierPortalFilament\Resources\PortalResourceResource;

final class ListPortalResources extends ListRecords
{
    protected static string $resource = PortalResourceResource::class;
}

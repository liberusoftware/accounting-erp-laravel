<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\VatFilament\Resources\VatReturnResource;

final class ListVatReturns extends ListRecords
{
    protected static string $resource = VatReturnResource::class;
}

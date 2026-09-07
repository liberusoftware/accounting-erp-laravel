<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\VatFilament\Resources\VatRecordResource;

final class ListVatRecords extends ListRecords
{
    protected static string $resource = VatRecordResource::class;
}

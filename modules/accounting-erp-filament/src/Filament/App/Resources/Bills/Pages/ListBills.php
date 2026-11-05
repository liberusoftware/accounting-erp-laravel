<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\BillResource;

class ListBills extends ListRecords
{
    #[\Override]
    protected static string $resource = BillResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\CreditMemos\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\CreditMemos\CreditMemoResource;

class ListCreditMemos extends ListRecords
{
    #[\Override]
    protected static string $resource = CreditMemoResource::class;
}

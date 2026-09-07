<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AccountsReceivableFilament\Resources\ReceivableOpenItemResource;

class ListReceivables extends ListRecords
{
    protected static string $resource = ReceivableOpenItemResource::class;
}

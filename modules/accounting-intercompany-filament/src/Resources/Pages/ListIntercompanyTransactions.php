<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\IntercompanyFilament\Resources\IntercompanyTransactionResource;

final class ListIntercompanyTransactions extends ListRecords
{
    protected static string $resource = IntercompanyTransactionResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\TransfersFilament\Resources\TransferResource;

final class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;
}

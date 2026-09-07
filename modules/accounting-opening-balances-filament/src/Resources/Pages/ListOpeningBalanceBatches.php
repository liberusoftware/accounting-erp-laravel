<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\OpeningBalancesFilament\Resources\OpeningBalanceBatchResource;

final class ListOpeningBalanceBatches extends ListRecords
{
    protected static string $resource = OpeningBalanceBatchResource::class;
}

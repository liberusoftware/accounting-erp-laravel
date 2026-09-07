<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ProjectProfitabilityFilament\Resources\ProjectProfitabilityResource;

final class ListProjectProfitability extends ListRecords
{
    protected static string $resource = ProjectProfitabilityResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\DelayedCharges\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\DelayedCharges\DelayedChargeResource;

class ListDelayedCharges extends ListRecords
{
    #[\Override]
    protected static string $resource = DelayedChargeResource::class;
}

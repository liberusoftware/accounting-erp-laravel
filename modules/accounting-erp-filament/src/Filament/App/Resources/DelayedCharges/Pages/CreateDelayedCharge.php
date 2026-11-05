<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\DelayedCharges\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\DelayedCharges\DelayedChargeResource;

class CreateDelayedCharge extends CreateRecord
{
    #[\Override]
    protected static string $resource = DelayedChargeResource::class;
}

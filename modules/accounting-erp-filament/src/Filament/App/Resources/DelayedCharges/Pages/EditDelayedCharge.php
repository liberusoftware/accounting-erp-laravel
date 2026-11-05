<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\DelayedCharges\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\DelayedCharges\DelayedChargeResource;

class EditDelayedCharge extends EditRecord
{
    #[\Override]
    protected static string $resource = DelayedChargeResource::class;
}

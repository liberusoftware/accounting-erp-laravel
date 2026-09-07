<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource;

final class CreateParty extends CreateRecord
{
    protected static string $resource = PartyResource::class;
}

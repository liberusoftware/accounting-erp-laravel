<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource;

final class EditParty extends EditRecord
{
    protected static string $resource = PartyResource::class;
}

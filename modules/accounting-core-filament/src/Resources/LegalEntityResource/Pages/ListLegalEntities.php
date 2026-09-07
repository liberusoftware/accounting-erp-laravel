<?php

namespace Liberu\Accounting\CoreFilament\Resources\LegalEntityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\CoreFilament\Resources\LegalEntityResource;

final class ListLegalEntities extends ListRecords
{
    protected static string $resource = LegalEntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

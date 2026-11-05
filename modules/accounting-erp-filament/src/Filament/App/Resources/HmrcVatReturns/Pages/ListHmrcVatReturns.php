<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\HmrcVatReturns\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\HmrcVatReturns\HmrcVatReturnResource;

class ListHmrcVatReturns extends ListRecords
{
    #[\Override]
    protected static string $resource = HmrcVatReturnResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

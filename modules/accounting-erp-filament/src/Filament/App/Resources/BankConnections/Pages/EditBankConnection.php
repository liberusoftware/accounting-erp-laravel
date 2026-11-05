<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\BankConnections\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\BankConnections\BankConnectionResource;

class EditBankConnection extends EditRecord
{
    #[\Override]
    protected static string $resource = BankConnectionResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

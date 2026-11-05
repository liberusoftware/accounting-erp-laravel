<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\BankStatements\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\BankStatements\BankStatementResource;

class ListBankStatements extends ListRecords
{
    #[\Override]
    protected static string $resource = BankStatementResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

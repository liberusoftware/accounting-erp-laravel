<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource;

final class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

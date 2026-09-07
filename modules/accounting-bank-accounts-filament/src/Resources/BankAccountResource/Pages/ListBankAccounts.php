<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource;

final class ListBankAccounts extends ListRecords
{
    protected static string $resource = BankAccountResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\BankAccounts\Actions\CreateBankAccount as CreateBankAccountAction;
use Liberu\Accounting\BankAccounts\Models\BankAccount;
use Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource;

final class CreateBankAccount extends CreateRecord
{
    protected static string $resource = BankAccountResource::class;

    protected function handleRecordCreation(array $data): BankAccount
    {
        return app(CreateBankAccountAction::class)->handle($data);
    }
}

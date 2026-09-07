<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ChartOfAccounts\Actions\SaveAccount;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource;

final class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SaveAccount::class)->handle($data);
    }
}

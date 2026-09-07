<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ChartOfAccounts\Actions\SaveAccount;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource;

final class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Account) {
            throw new \InvalidArgumentException('The selected record is not an account.');
        }

        return app(SaveAccount::class)->handle($data, $record);
    }
}

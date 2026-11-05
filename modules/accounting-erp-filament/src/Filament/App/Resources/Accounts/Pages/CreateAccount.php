<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Accounts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Accounts\AccountResource;

class CreateAccount extends CreateRecord
{
    #[\Override]
    protected static string $resource = AccountResource::class;
}

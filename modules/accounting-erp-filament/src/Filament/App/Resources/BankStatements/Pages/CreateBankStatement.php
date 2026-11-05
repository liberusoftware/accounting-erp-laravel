<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\BankStatements\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\BankStatements\BankStatementResource;

class CreateBankStatement extends CreateRecord
{
    #[\Override]
    protected static string $resource = BankStatementResource::class;
}

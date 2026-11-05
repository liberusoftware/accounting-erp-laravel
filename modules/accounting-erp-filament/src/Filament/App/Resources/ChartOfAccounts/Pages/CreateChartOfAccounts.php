<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ChartOfAccounts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ChartOfAccounts\ChartOfAccountsResource;

class CreateChartOfAccounts extends CreateRecord
{
    #[\Override]
    protected static string $resource = ChartOfAccountsResource::class;
}

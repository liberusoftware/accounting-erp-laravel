<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\CreditMemos\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\CreditMemos\CreditMemoResource;

class CreateCreditMemo extends CreateRecord
{
    #[\Override]
    protected static string $resource = CreditMemoResource::class;
}

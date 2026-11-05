<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\CreditMemos\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\CreditMemos\CreditMemoResource;

class EditCreditMemo extends EditRecord
{
    #[\Override]
    protected static string $resource = CreditMemoResource::class;
}

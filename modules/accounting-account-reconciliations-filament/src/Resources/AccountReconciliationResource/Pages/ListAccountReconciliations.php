<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsFilament\Resources\AccountReconciliationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AccountReconciliationsFilament\Resources\AccountReconciliationResource;

final class ListAccountReconciliations extends ListRecords
{
    protected static string $resource = AccountReconciliationResource::class;
}

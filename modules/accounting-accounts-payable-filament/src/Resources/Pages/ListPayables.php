<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AccountsPayableFilament\Resources\PayableOpenItemResource;

final class ListPayables extends ListRecords
{
    protected static string $resource = PayableOpenItemResource::class;
}

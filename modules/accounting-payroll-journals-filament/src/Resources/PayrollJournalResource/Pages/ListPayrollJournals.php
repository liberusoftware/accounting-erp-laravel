<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsFilament\Resources\PayrollJournalResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PayrollJournalsFilament\Resources\PayrollJournalResource;

final class ListPayrollJournals extends ListRecords
{
    protected static string $resource = PayrollJournalResource::class;
}

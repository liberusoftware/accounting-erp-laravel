<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\RecurringTransactionsFilament\Resources\RecurringTemplateResource;

final class ListRecurringTemplates extends ListRecords
{
    protected static string $resource = RecurringTemplateResource::class;
}

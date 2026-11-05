<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\JournalEntries\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\JournalEntries\JournalEntryResource;

class ListJournalEntries extends ListRecords
{
    #[\Override]
    protected static string $resource = JournalEntryResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

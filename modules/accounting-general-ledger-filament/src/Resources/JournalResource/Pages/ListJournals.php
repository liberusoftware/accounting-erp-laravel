<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource;

final class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;
}

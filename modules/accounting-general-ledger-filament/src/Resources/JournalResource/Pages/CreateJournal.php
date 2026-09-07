<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\GeneralLedger\Actions\CreateJournal as CreateJournalAction;
use Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource;

final class CreateJournal extends CreateRecord
{
    protected static string $resource = JournalResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        return app(CreateJournalAction::class)->handle($data, $lines);
    }
}

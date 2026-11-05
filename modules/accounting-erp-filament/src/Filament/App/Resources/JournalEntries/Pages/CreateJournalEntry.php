<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\JournalEntries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Rules\DoubleEntryValidator;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\JournalEntries\JournalEntryResource;

class CreateJournalEntry extends CreateRecord
{
    #[\Override]
    protected static string $resource = JournalEntryResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    protected function beforeValidate(): void
    {
        $lines = $this->data['lines'] ?? [];

        $validator = new DoubleEntryValidator($lines);

        if (! $validator->passes('lines', $lines)) {
            $this->addError('lines', $validator->message());
        }
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace Liberu\Accounting\CoreFilament\Resources\BookResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\CoreFilament\Resources\BookResource;

final class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

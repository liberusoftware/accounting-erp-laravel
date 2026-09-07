<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\CarbonAccountingFilament\Resources\CarbonActivityResource;

final class CreateCarbonActivity extends CreateRecord
{
    protected static string $resource = CarbonActivityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

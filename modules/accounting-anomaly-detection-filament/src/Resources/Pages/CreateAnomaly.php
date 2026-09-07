<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\AnomalyDetectionFilament\Resources\AnomalyResource;

final class CreateAnomaly extends CreateRecord
{
    protected static string $resource = AnomalyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

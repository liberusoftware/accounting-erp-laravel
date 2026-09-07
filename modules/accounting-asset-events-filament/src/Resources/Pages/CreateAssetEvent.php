<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\AssetEventsFilament\Resources\AssetEventResource;

final class CreateAssetEvent extends CreateRecord
{
    protected static string $resource = AssetEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0), 'actor_id' => (int) (auth()->id() ?? 0)];
    }
}

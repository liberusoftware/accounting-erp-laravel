<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\BusinessInsightsFilament\Resources\InsightSnapshotResource;

final class CreateInsightSnapshot extends CreateRecord
{
    protected static string $resource = InsightSnapshotResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

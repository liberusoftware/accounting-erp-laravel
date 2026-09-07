<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReviewFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ReviewFilament\Resources\ReviewItemResource;

final class CreateReviewItem extends CreateRecord
{
    protected static string $resource = ReviewItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

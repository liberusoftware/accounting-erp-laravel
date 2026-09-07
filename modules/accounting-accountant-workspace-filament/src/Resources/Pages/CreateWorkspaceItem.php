<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\AccountantWorkspaceFilament\Resources\WorkspaceItemResource;

final class CreateWorkspaceItem extends CreateRecord
{
    protected static string $resource = WorkspaceItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

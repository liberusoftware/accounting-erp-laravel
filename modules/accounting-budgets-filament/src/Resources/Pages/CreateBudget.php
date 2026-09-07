<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\BudgetsFilament\Resources\BudgetResource;

final class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

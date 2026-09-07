<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\AuditSupportFilament\Resources\AuditRequestResource;

final class CreateAuditRequest extends CreateRecord
{
    protected static string $resource = AuditRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) (auth()->user()?->current_team_id ?? 0)];
    }
}

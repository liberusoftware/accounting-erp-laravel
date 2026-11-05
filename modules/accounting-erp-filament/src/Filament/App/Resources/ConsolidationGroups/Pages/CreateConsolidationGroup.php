<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ConsolidationGroups\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ConsolidationGroups\ConsolidationGroupResource;

/**
 * @property-read ConsolidationGroup $record
 */
class CreateConsolidationGroup extends CreateRecord
{
    #[\Override]
    protected static string $resource = ConsolidationGroupResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // The current tenant team owns the group.
        $data['owner_team_id'] = Filament::getTenant()?->getKey();

        return $data;
    }

    protected function afterCreate(): void
    {
        ConsolidationGroupResource::enforceAllowedMembers($this->record);
    }
}

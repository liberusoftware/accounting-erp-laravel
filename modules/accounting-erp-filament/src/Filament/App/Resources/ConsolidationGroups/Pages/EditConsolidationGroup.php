<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ConsolidationGroups\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ConsolidationGroups\ConsolidationGroupResource;

/**
 * @property-read ConsolidationGroup $record
 */
class EditConsolidationGroup extends EditRecord
{
    #[\Override]
    protected static string $resource = ConsolidationGroupResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        ConsolidationGroupResource::enforceAllowedMembers($this->record);
    }
}

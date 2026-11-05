<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ReconciliationRules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ReconciliationRules\ReconciliationRuleResource;

class ListReconciliationRules extends ListRecords
{
    #[\Override]
    protected static string $resource = ReconciliationRuleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ReconciliationRules\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ReconciliationRules\ReconciliationRuleResource;

class CreateReconciliationRule extends CreateRecord
{
    #[\Override]
    protected static string $resource = ReconciliationRuleResource::class;
}

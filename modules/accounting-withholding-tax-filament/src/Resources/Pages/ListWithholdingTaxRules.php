<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\WithholdingTaxFilament\Resources\WithholdingTaxRuleResource;

final class ListWithholdingTaxRules extends ListRecords
{
    protected static string $resource = WithholdingTaxRuleResource::class;
}

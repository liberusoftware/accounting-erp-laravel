<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreFilament\Resources\TaxRuleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\TaxCoreFilament\Resources\TaxRuleResource;

final class ListTaxRules extends ListRecords
{
    protected static string $resource = TaxRuleResource::class;
}

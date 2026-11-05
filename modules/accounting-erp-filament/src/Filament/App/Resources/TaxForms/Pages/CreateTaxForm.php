<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\TaxForms\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\TaxForms\TaxFormResource;

class CreateTaxForm extends CreateRecord
{
    #[\Override]
    protected static string $resource = TaxFormResource::class;
}

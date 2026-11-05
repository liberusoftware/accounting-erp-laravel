<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Companies\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Companies\CompanyResource;

class CreateCompany extends CreateRecord
{
    #[\Override]
    protected static string $resource = CompanyResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Customers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Customers\CustomerResource;

class CreateCustomer extends CreateRecord
{
    #[\Override]
    protected static string $resource = CustomerResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Payrolls\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Payrolls\PayrollResource;

class CreatePayroll extends CreateRecord
{
    #[\Override]
    protected static string $resource = PayrollResource::class;
}

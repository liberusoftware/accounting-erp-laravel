<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\PaymentTerms\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\PaymentTerms\PaymentTermResource;

class CreatePaymentTerm extends CreateRecord
{
    #[\Override]
    protected static string $resource = PaymentTermResource::class;
}

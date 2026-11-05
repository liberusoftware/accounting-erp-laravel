<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Payments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Payments\PaymentResource;

class CreatePayment extends CreateRecord
{
    #[\Override]
    protected static string $resource = PaymentResource::class;
}

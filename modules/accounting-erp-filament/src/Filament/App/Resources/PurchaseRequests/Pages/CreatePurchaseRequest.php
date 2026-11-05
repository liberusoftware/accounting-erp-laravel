<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\PurchaseRequests\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\PurchaseRequests\PurchaseRequestResource;

class CreatePurchaseRequest extends CreateRecord
{
    #[\Override]
    protected static string $resource = PurchaseRequestResource::class;
}

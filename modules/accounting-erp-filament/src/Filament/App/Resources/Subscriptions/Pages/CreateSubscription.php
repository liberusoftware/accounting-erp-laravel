<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Subscriptions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Subscriptions\SubscriptionResource;

class CreateSubscription extends CreateRecord
{
    #[\Override]
    protected static string $resource = SubscriptionResource::class;
}

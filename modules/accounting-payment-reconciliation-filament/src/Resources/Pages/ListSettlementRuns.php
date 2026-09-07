<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PaymentReconciliationFilament\Resources\SettlementRunResource;

final class ListSettlementRuns extends ListRecords
{
    protected static string $resource = SettlementRunResource::class;
}

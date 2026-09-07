<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ReimbursementsFilament\Resources\ReimbursementLiabilityResource;

final class ListReimbursementLiabilities extends ListRecords
{
    protected static string $resource = ReimbursementLiabilityResource::class;
}

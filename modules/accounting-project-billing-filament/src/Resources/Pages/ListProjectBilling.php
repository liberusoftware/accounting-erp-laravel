<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ProjectBillingFilament\Resources\ProjectBillingResource;

final class ListProjectBilling extends ListRecords
{
    protected static string $resource = ProjectBillingResource::class;
}

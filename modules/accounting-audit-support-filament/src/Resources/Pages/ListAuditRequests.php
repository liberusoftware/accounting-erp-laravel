<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AuditSupportFilament\Resources\AuditRequestResource;

final class ListAuditRequests extends ListRecords
{
    protected static string $resource = AuditRequestResource::class;
}

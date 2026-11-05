<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\AuditLogs\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\AuditLogs\AuditLogResource;

class ListAuditLogs extends ListRecords
{
    #[\Override]
    protected static string $resource = AuditLogResource::class;
}

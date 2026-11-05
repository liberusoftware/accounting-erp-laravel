<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\AuditLogs\Pages;

use Filament\Resources\Pages\ViewRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\AuditLogs\AuditLogResource;

class ViewAuditLog extends ViewRecord
{
    #[\Override]
    protected static string $resource = AuditLogResource::class;
}

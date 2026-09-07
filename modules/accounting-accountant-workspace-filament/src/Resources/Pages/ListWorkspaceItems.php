<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AccountantWorkspaceFilament\Resources\WorkspaceItemResource;

final class ListWorkspaceItems extends ListRecords
{
    protected static string $resource = WorkspaceItemResource::class;
}

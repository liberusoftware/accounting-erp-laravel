<?php

declare(strict_types=1);

namespace Liberu\Accounting\CopilotFilament\Resources\CopilotRequestResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\CopilotFilament\Resources\CopilotRequestResource;

final class ListCopilotRequests extends ListRecords
{
    protected static string $resource = CopilotRequestResource::class;
}

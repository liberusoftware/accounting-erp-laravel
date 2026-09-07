<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingFilament\Resources\BranchResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\BranchLocationAccountingFilament\Resources\BranchResource;

final class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;
}

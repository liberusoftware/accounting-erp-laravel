<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\JournalApprovalsFilament\Resources\JournalApprovalResource;

final class ListJournalApprovals extends ListRecords
{
    protected static string $resource = JournalApprovalResource::class;
}

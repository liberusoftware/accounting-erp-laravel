<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Enums\RecordStatus;
use Liberu\Accounting\FinancialMasterData\Events\MasterRecordArchived;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class ArchiveParty
{
    public function __construct(private readonly Dispatcher $events) {}

    public function handle(Party $party): Party
    {
        if ($party->status === RecordStatus::Archived) {
            throw new DuplicateMasterRecord('The party is already archived.');
        }

        return DB::transaction(function () use ($party): Party {
            $party->update(['status' => RecordStatus::Archived]);
            $party = $party->refresh();
            $this->events->dispatch(new MasterRecordArchived($party));

            return $party;
        });
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Enums\RecordStatus;
use Liberu\Accounting\FinancialMasterData\Events\MasterRecordsMerged;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class MergeDuplicateParties
{
    public function handle(Party $survivor, Party $duplicate): Party
    {
        if ($survivor->is($duplicate) || $survivor->legal_entity_id !== $duplicate->legal_entity_id || $survivor->type !== $duplicate->type) {
            throw new DuplicateMasterRecord('Only duplicate parties from the same legal entity and type can be merged.');
        }
        if ($survivor->status === RecordStatus::Archived || $duplicate->status === RecordStatus::Archived) {
            throw new DuplicateMasterRecord('Archived parties cannot participate in a merge.');
        }

        $survivor = DB::transaction(function () use ($survivor, $duplicate): Party {
            $duplicate->addresses()->update(['party_id' => $survivor->getKey()]);
            $duplicate->bankDetails()->update(['party_id' => $survivor->getKey()]);
            $metadata = array_merge($duplicate->metadata ?? [], ['merged_into' => $survivor->getKey(), 'merged_at' => now()->toIso8601String()]);
            $duplicate->update(['status' => RecordStatus::Archived, 'metadata' => $metadata]);
            $survivor = $survivor->refresh();
            DB::afterCommit(fn (): mixed => event(new MasterRecordsMerged($survivor, $duplicate->refresh())));

            return $survivor;
        });

        return $survivor;
    }
}

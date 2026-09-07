<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\OpeningBalances\Enums\EntryStatus;
use Liberu\Accounting\OpeningBalances\Enums\OpeningBalanceStatus;
use Liberu\Accounting\OpeningBalances\Events\OpeningBalancesValidated;
use Liberu\Accounting\OpeningBalances\Exceptions\InvalidOpeningBalance;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceEntry;

final class ValidateOpeningBalances
{
    public function handle(OpeningBalanceBatch $batch): OpeningBalanceBatch
    {
        if (! in_array($batch->status, [OpeningBalanceStatus::Draft, OpeningBalanceStatus::Failed], true)) {
            throw new InvalidOpeningBalance('Only draft or failed batches can be validated.');
        }
        try {
            return DB::transaction(function () use ($batch): OpeningBalanceBatch {
                /** @var OpeningBalanceBatch $lockedBatch */
                $lockedBatch = OpeningBalanceBatch::query()->lockForUpdate()->findOrFail($batch->id);
                if (! in_array($lockedBatch->status, [OpeningBalanceStatus::Draft, OpeningBalanceStatus::Failed], true)) {
                    throw new InvalidOpeningBalance('Only draft or failed batches can be validated.');
                }
                $entries = $lockedBatch->entries()->get();
                $errors = [];
                foreach ($entries->groupBy('currency') as $currency => $rows) {
                    $debit = round((float) $rows->sum('debit_amount'), 2);
                    $credit = round((float) $rows->sum('credit_amount'), 2);
                    if (abs($debit - $credit) > 0.01) {
                        $errors[] = "{$currency} is out of balance by ".round($debit - $credit, 2);
                    }
                }
                $duplicate = $entries->groupBy(fn (OpeningBalanceEntry $entry): string => $entry->balance_type->value.':'.$entry->reference_id.':'.($entry->document_ref ?? ''))->filter(fn ($rows): bool => $rows->count() > 1);
                if ($duplicate->isNotEmpty()) {
                    $errors[] = 'Duplicate balance references were supplied.';
                }
                if ($errors !== []) {
                    throw new InvalidOpeningBalance(implode(' ', $errors));
                }
                $lockedBatch->update(['status' => OpeningBalanceStatus::Validated, 'failure_message' => null, 'summary' => ['entry_count' => $entries->count(), 'debit_total' => (float) $entries->sum('debit_amount'), 'credit_total' => (float) $entries->sum('credit_amount')]]);
                $lockedBatch->entries()->update(['status' => EntryStatus::Valid]);
                $lockedBatch->audits()->create(['event_type' => 'opening_balances_validated', 'payload' => $lockedBatch->summary, 'payload_hash' => hash('sha256', json_encode($lockedBatch->summary, JSON_THROW_ON_ERROR)), 'created_at' => now()]);
                $result = $lockedBatch->refresh()->load('entries');
                DB::afterCommit(fn () => event(new OpeningBalancesValidated($result)));

                return $result;
            });
        } catch (InvalidOpeningBalance $exception) {
            OpeningBalanceBatch::query()->whereKey($batch->id)->update(['status' => OpeningBalanceStatus::Failed, 'failure_message' => $exception->getMessage()]);
            $batch->entries()->update(['status' => EntryStatus::Exception]);
            throw $exception;
        }
    }
}

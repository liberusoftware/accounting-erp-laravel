<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PaymentReconciliation\Enums\ReconciliationExceptionStatus;
use Liberu\Accounting\PaymentReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class IdentifyMissingItems
{
    /** @param array<int,array{external_ref:string,expected_amount?:float,currency?:string}> $expected */
    public function handle(SettlementRun $run, array $expected): array
    {
        return DB::transaction(function () use ($run, $expected): array {
            $created = [];
            foreach ($expected as $record) {
                $ref = trim($record['external_ref']);
                if ($ref === '') {
                    throw new InvalidReconciliation('Expected item reference is required.');
                }if (! $run->items()->where('external_ref', $ref)->exists() && ! $run->exceptions()->where('kind', 'missing_item')->where('external_ref', $ref)->exists()) {
                    $created[] = $run->exceptions()->create(['kind' => 'missing_item', 'external_ref' => $ref, 'expected_amount' => $record['expected_amount'] ?? null, 'currency' => $record['currency'] ?? $run->currency, 'status' => ReconciliationExceptionStatus::Open, 'severity' => 'blocking', 'metadata' => ['source' => 'expected_items']]);
                }
            }if ($created) {
                $run->update(['status' => 'exception']);
            }

            return $created;
        });
    }
}

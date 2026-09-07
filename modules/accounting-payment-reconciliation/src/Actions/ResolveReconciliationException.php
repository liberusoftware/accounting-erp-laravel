<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PaymentReconciliation\Enums\ReconciliationExceptionStatus;
use Liberu\Accounting\PaymentReconciliation\Events\ReconciliationExceptionResolved;
use Liberu\Accounting\PaymentReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\PaymentReconciliation\Models\ReconciliationException;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class ResolveReconciliationException
{
    public function handle(ReconciliationException $exception, int $actorId, string $resolution, bool $waive = false): ReconciliationException
    {
        return DB::transaction(function () use ($exception, $actorId, $resolution, $waive): ReconciliationException {
            if ($exception->status !== ReconciliationExceptionStatus::Open || blank($resolution)) {
                throw new InvalidReconciliation('Only open exceptions can be resolved and a resolution is required.');
            }$exception->update(['status' => $waive ? ReconciliationExceptionStatus::Waived : ReconciliationExceptionStatus::Resolved, 'resolution' => $resolution, 'resolved_by' => $actorId, 'resolved_at' => now()]); /** @var SettlementRun $run */
            $run = $exception->run()->firstOrFail();
            $run->audits()->create(['event_type' => 'reconciliation_exception_resolved', 'actor_id' => $actorId, 'payload' => ['exception_id' => $exception->id, 'waived' => $waive], 'payload_hash' => hash('sha256', $resolution), 'created_at' => now()]);
            $result = $exception->refresh();
            DB::afterCommit(fn () => event(new ReconciliationExceptionResolved($result)));

            return $result;
        });
    }
}

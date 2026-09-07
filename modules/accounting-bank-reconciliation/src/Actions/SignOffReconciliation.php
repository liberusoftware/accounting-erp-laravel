<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationStatus;
use Liberu\Accounting\BankReconciliation\Events\ReconciliationSignedOff;
use Liberu\Accounting\BankReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;
use Liberu\Accounting\BankReconciliation\Queries\ReconciliationSummaryQuery;

final class SignOffReconciliation
{
    public function handle(ReconciliationSession $session): ReconciliationSession
    {
        return DB::transaction(function () use ($session): ReconciliationSession {
            $session = ReconciliationSession::query()->lockForUpdate()->findOrFail($session->getKey());
            if ($session->status === ReconciliationStatus::SignedOff) {
                return $session;
            }
            $summary = app(ReconciliationSummaryQuery::class)->handle($session);
            if ($summary['exceptions'] > 0 || abs($summary['variance']) >= 0.01) {
                throw new InvalidReconciliation('A reconciliation must have no exceptions and zero balance variance before sign-off.');
            }
            $session->update(['status' => ReconciliationStatus::SignedOff, 'signed_off_at' => now(), 'signed_off_by' => auth()->id()]);
            DB::afterCommit(fn (): mixed => event(new ReconciliationSignedOff($session->refresh())));

            return $session->refresh();
        });
    }
}

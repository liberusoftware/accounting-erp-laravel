<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Periods\Events\PeriodLockChanged;
use Liberu\Accounting\Periods\Exceptions\InvalidPeriodTransition;
use Liberu\Accounting\Periods\Models\AccountingPeriod;

final class UnlockPeriod
{
    public function __construct(private readonly Dispatcher $events) {}

    public function handle(AccountingPeriod $period, ?string $actor, ?string $reason): AccountingPeriod
    {
        if (blank($reason)) {
            throw new InvalidPeriodTransition('Unlocking a period requires a reason.');
        }

        return DB::transaction(function () use ($period, $actor, $reason): AccountingPeriod {
            $period = AccountingPeriod::query()->lockForUpdate()->findOrFail($period->getKey());
            if ($period->locked_at === null) {
                return $period;
            }
            $evidence = $period->evidence ?? [];
            $evidence[] = ['action' => 'unlock', 'actor' => $actor, 'reason' => $reason, 'at' => now()->toIso8601String()];
            $period->forceFill(['locked_by' => null, 'locked_at' => null, 'evidence' => $evidence])->save();
            $period->refresh();
            DB::afterCommit(fn () => $this->events->dispatch(new PeriodLockChanged($period, false)));

            return $period;
        });
    }
}

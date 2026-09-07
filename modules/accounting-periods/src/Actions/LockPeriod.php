<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Periods\Enums\PeriodState;
use Liberu\Accounting\Periods\Events\PeriodLockChanged;
use Liberu\Accounting\Periods\Exceptions\InvalidPeriodTransition;
use Liberu\Accounting\Periods\Models\AccountingPeriod;

final class LockPeriod
{
    public function __construct(private readonly Dispatcher $events) {}

    public function handle(AccountingPeriod $period, ?string $actor): AccountingPeriod
    {
        return DB::transaction(function () use ($period, $actor): AccountingPeriod {
            $period = AccountingPeriod::query()->lockForUpdate()->findOrFail($period->getKey());
            if ($period->state !== PeriodState::HardClosed) {
                throw new InvalidPeriodTransition('Only hard-closed periods may be locked.');
            }
            if ($period->locked_at !== null) {
                return $period;
            }
            $period->forceFill(['locked_by' => $actor, 'locked_at' => now()])->save();
            $period->refresh();
            DB::afterCommit(fn () => $this->events->dispatch(new PeriodLockChanged($period, true)));

            return $period;
        });
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Periods\Enums\PeriodState;
use Liberu\Accounting\Periods\Events\PeriodStateChanged;
use Liberu\Accounting\Periods\Exceptions\InvalidPeriodTransition;
use Liberu\Accounting\Periods\Models\AccountingPeriod;

final class TransitionPeriod
{
    public function __construct(private readonly Dispatcher $events) {}

    public function handle(AccountingPeriod $period, PeriodState $to, ?string $actor = null, ?string $reason = null): AccountingPeriod
    {
        return DB::transaction(function () use ($period, $to, $actor, $reason): AccountingPeriod {
            $period = AccountingPeriod::query()->lockForUpdate()->findOrFail($period->getKey());
            $from = $period->state;
            $allowed = ($from === PeriodState::Open && $to === PeriodState::SoftClosed) || ($from === PeriodState::SoftClosed && in_array($to, [PeriodState::Open, PeriodState::HardClosed], true));
            if (! $allowed) {
                throw new InvalidPeriodTransition("Cannot transition period from {$from->value} to {$to->value}.");
            }
            if ($to === PeriodState::Open && blank($reason)) {
                throw new InvalidPeriodTransition('Reopening a period requires a reason.');
            }
            $period->state = $to;
            if ($to === PeriodState::Open) {
                $period->reopened_by = $actor;
                $period->reopen_reason = $reason;
            } $period->save();
            DB::afterCommit(fn () => $this->events->dispatch(new PeriodStateChanged($period, $from, $to)));

            return $period->refresh();
        });
    }
}

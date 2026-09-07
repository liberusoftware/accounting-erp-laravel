<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingDepositStatus;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingFundStatus;
use Liberu\Accounting\DepositsAndClearing\Exceptions\InvalidClearing;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingDeposit;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingFund;

final class CreateGroupedDeposit
{
    public function handle(array $attributes, array $fundIds): ClearingDeposit
    {
        foreach (['team_id', 'deposit_ref', 'account_ref', 'currency', 'deposit_date'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidClearing("{$field} is required.");
            }
        }
        if ($fundIds === []) {
            throw new InvalidClearing('At least one undeposited fund is required.');
        }

        return DB::transaction(function () use ($attributes, $fundIds): ClearingDeposit {
            $funds = ClearingFund::query()->where('team_id', $attributes['team_id'])->whereIn('id', $fundIds)->where('status', ClearingFundStatus::Undeposited)->lockForUpdate()->get();
            if ($funds->count() !== count(array_unique($fundIds)) || $funds->pluck('currency')->unique()->count() !== 1 || $funds->first()->currency !== $attributes['currency']) {
                throw new InvalidClearing('Funds must be available, unique, and in the deposit currency.');
            }
            $deposit = ClearingDeposit::create([...$attributes, 'gross_amount' => $funds->sum('amount'), 'payout_amount' => $funds->sum('amount'), 'status' => ClearingDepositStatus::Open]);
            $funds->each->update(['deposit_id' => $deposit->getKey(), 'status' => ClearingFundStatus::Grouped]);

            return $deposit->refresh()->load('funds');
        });
    }
}

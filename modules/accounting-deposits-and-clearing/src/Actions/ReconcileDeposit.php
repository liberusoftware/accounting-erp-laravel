<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingDepositStatus;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingFundStatus;
use Liberu\Accounting\DepositsAndClearing\Exceptions\InvalidClearing;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingDeposit;

final class ReconcileDeposit
{
    public function handle(ClearingDeposit $deposit, float $payoutAmount, float $feeAmount = 0): ClearingDeposit
    {
        if ($payoutAmount < 0 || $feeAmount < 0 || abs(($payoutAmount + $feeAmount) - (float) $deposit->gross_amount) > 0.01) {
            throw new InvalidClearing('Payout plus fees must equal the gross deposit.');
        }

        return DB::transaction(function () use ($deposit, $payoutAmount, $feeAmount): ClearingDeposit {
            $deposit->update(['fee_amount' => $feeAmount, 'payout_amount' => $payoutAmount, 'status' => ClearingDepositStatus::Reconciled]);
            $deposit->funds()->update(['status' => ClearingFundStatus::Reconciled]);

            return $deposit->fresh('funds');
        });
    }
}

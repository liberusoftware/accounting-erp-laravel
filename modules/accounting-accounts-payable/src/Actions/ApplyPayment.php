<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\AccountsPayable\Events\PaymentApplied;
use Liberu\Accounting\AccountsPayable\Exceptions\InvalidPayable;
use Liberu\Accounting\AccountsPayable\Models\PayableAccount;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;

final class ApplyPayment
{
    public function handle(PayablePayment $payment, PayableOpenItem $item, float $amount): PayablePayment
    {
        return DB::transaction(function () use ($payment, $item, $amount) {
            $payment = PayablePayment::lockForUpdate()->findOrFail($payment->id);
            $item = PayableOpenItem::lockForUpdate()->findOrFail($item->id);
            $account = PayableAccount::query()->where('party_id', $item->party_id)->first();
            if ($amount <= 0 || ($payment->party_id !== null && $payment->party_id !== $item->party_id) || $payment->currency !== $item->currency || $account?->payment_hold || $amount > $payment->unapplied() || $amount > $item->outstanding()) {
                throw new InvalidPayable('Payment application must match the supplier and remain within both outstanding balances.');
            }
            if ($payment->applications()->where('open_item_id', $item->id)->exists()) {
                throw new InvalidPayable('A payment can only be applied to the same open item once.');
            }
            $payment->applications()->create(['open_item_id' => $item->id, 'amount' => $amount]);
            $payment->applied_amount = (float) $payment->applied_amount + $amount;
            $payment->status = $payment->unapplied() <= 0 ? PayableStatus::Applied : PayableStatus::Partial;
            $payment->save();
            $item->paid_amount = (float) $item->paid_amount + $amount;
            $item->status = $item->outstanding() <= 0 ? PayableStatus::Settled : PayableStatus::Partial;
            $item->save();
            app(RecalculateAccountBalance::class)->handle((int) $item->party_id);
            DB::afterCommit(fn (): mixed => event(new PaymentApplied(PayablePayment::query()->findOrFail($payment->getKey()), PayableOpenItem::query()->findOrFail($item->getKey()), $amount)));

            return $payment->refresh()->load('applications');
        });
    }
}

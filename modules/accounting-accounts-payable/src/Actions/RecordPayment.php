<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\AccountsPayable\Events\PaymentRecorded;
use Liberu\Accounting\AccountsPayable\Exceptions\InvalidPayable;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class RecordPayment
{
    public function handle(array $attributes): PayablePayment
    {
        $attributes['currency'] = Str::upper((string) ($attributes['currency'] ?? ''));
        if ((float) ($attributes['amount'] ?? 0) <= 0 || strlen($attributes['currency']) !== 3) {
            throw new InvalidPayable('A payment requires a positive amount and currency.');
        }

        return DB::transaction(function () use ($attributes): PayablePayment {
            if (! empty($attributes['party_id']) && ! Party::query()->whereKey($attributes['party_id'])->where('type', PartyType::Supplier)->exists()) {
                throw new InvalidPayable('Payments can only be assigned to an existing supplier.');
            }
            $payment = PayablePayment::create(array_merge($attributes, ['applied_amount' => 0, 'status' => PayableStatus::Unapplied, 'paid_on' => $attributes['paid_on'] ?? now()->toDateString()]));
            DB::afterCommit(fn (): mixed => event(new PaymentRecorded(PayablePayment::query()->findOrFail($payment->getKey()))));

            return $payment->refresh();
        });
    }
}

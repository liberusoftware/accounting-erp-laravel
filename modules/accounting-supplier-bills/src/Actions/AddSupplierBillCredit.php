<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class AddSupplierBillCredit
{
    public function handle(SupplierBill $bill, array $attributes): SupplierBill
    {
        return DB::transaction(function () use ($bill, $attributes): SupplierBill {
            $bill = SupplierBill::query()->lockForUpdate()->findOrFail($bill->id);
            $amount = (float) ($attributes['amount'] ?? 0);
            $currency = strtoupper((string) ($attributes['currency'] ?? $bill->currency));
            if (! in_array($bill->status, [SupplierBillStatus::Approved, SupplierBillStatus::Posted], true) || $amount <= 0 || $amount > $bill->outstanding() || blank($attributes['reason'] ?? null) || $currency !== $bill->currency) {
                throw new InvalidSupplierBill('A credit requires an approved or posted bill, a reason, and an amount within the outstanding balance.');
            }
            $bill->credits()->create(array_merge($attributes, ['amount' => $amount, 'currency' => $currency]));

            return $bill->refresh()->load('credits');
        });
    }
}

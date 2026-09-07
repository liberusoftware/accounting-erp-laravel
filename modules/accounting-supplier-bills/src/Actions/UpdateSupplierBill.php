<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class UpdateSupplierBill
{
    public function handle(SupplierBill $bill, array $attributes, array $lines): SupplierBill
    {
        return DB::transaction(function () use ($bill, $attributes, $lines): SupplierBill {
            $bill = SupplierBill::query()->lockForUpdate()->findOrFail($bill->id);
            if ($bill->status !== SupplierBillStatus::Draft) {
                throw new InvalidSupplierBill('Only draft supplier bills may be changed.');
            }
            if ($lines === []) {
                throw new InvalidSupplierBill('An updated supplier bill requires at least one line.');
            }
            $subtotal = 0.0;
            $taxTotal = 0.0;
            $normalized = [];
            foreach ($lines as $line) {
                $quantity = (float) ($line['quantity'] ?? 0);
                $unitPrice = (float) ($line['unit_price'] ?? 0);
                $discountRate = (float) ($line['discount_rate'] ?? 0);
                $taxRate = (float) ($line['tax_rate'] ?? 0);
                if (blank($line['description'] ?? null) || $quantity <= 0 || $unitPrice < 0 || $discountRate < 0 || $discountRate > 100 || $taxRate < 0) {
                    throw new InvalidSupplierBill('Bill lines have invalid values.');
                }
                $gross = round($quantity * $unitPrice, 2);
                $net = round($gross - ($gross * $discountRate / 100), 2);
                $tax = round($net * $taxRate / 100, 2);
                $subtotal += $net;
                $taxTotal += $tax;
                $normalized[] = array_merge($line, ['quantity' => $quantity, 'unit_price' => $unitPrice, 'discount_rate' => $discountRate, 'tax_rate' => $taxRate, 'net_amount' => $net, 'tax_amount' => $tax]);
            }
            $bill->update(array_merge($attributes, ['subtotal' => round($subtotal, 2), 'tax_total' => round($taxTotal, 2), 'total' => round($subtotal + $taxTotal, 2)]));
            $bill->lines()->delete();
            $bill->lines()->createMany($normalized);

            return $bill->refresh()->load('lines');
        });
    }
}

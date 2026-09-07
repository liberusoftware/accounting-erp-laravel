<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SupplierBills\Enums\PaymentStatus;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Events\SupplierBillCreated;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class CreateSupplierBill
{
    public function handle(array $attributes, array $lines): SupplierBill
    {
        return DB::transaction(function () use ($attributes, $lines): SupplierBill {
            if ($lines === [] || empty($attributes['party_id']) || empty($attributes['bill_date']) || blank($attributes['currency'] ?? null)) {
                throw new InvalidSupplierBill('A supplier bill requires a supplier, bill date, currency, and at least one line.');
            }
            if (! Party::query()->whereKey($attributes['party_id'])->where('type', PartyType::Supplier)->exists()) {
                throw new InvalidSupplierBill('Supplier bills can only be created for an existing supplier.');
            }
            if (($attributes['due_on'] ?? null) !== null && $attributes['due_on'] < $attributes['bill_date']) {
                throw new InvalidSupplierBill('A supplier bill due date cannot precede its bill date.');
            }
            $attributes['currency'] = Str::upper((string) $attributes['currency']);
            if (strlen($attributes['currency']) !== 3) {
                throw new InvalidSupplierBill('A supplier bill currency must be a three-letter code.');
            }
            $billNumber = $attributes['bill_number'] ?? 'BILL-'.now()->format('Y').'-'.Str::upper(Str::random(10));
            if (SupplierBill::query()->where('party_id', $attributes['party_id'])->where('bill_number', $billNumber)->exists()) {
                throw new InvalidSupplierBill('A bill with this supplier bill number already exists.');
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
                    throw new InvalidSupplierBill('Bill lines have invalid descriptions, quantities, prices, discounts, or tax rates.');
                }
                $gross = round($quantity * $unitPrice, 2);
                $net = round($gross - ($gross * $discountRate / 100), 2);
                $tax = round($net * $taxRate / 100, 2);
                $subtotal += $net;
                $taxTotal += $tax;
                $normalized[] = array_merge($line, ['quantity' => $quantity, 'unit_price' => $unitPrice, 'discount_rate' => $discountRate, 'tax_rate' => $taxRate, 'net_amount' => $net, 'tax_amount' => $tax]);
            }
            $bill = SupplierBill::create(array_merge($attributes, ['bill_number' => $billNumber, 'status' => SupplierBillStatus::Draft, 'payment_status' => PaymentStatus::Unpaid, 'subtotal' => round($subtotal, 2), 'tax_total' => round($taxTotal, 2), 'total' => round($subtotal + $taxTotal, 2), 'approval_status' => 'pending']));
            $bill->lines()->createMany($normalized);
            $bill = $bill->load('lines');
            DB::afterCommit(fn () => event(new SupplierBillCreated($bill)));

            return $bill;
        });
    }
}

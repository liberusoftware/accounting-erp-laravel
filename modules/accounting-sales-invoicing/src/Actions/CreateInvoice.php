<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Events\InvoiceCreated;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class CreateInvoice
{
    public function handle(array $attributes, array $lines): SalesInvoice
    {
        return DB::transaction(function () use ($attributes, $lines) {
            if ($lines === []) {
                throw new InvalidInvoice('An invoice requires at least one line.');
            }$subtotal = 0;
            $discount = 0;
            $tax = 0;
            $normalized = [];
            foreach ($lines as $line) {
                $quantity = (float) ($line['quantity'] ?? 0);
                $unit = (float) ($line['unit_price'] ?? 0);
                $discountRate = (float) ($line['discount_rate'] ?? 0);
                $taxRate = (float) ($line['tax_rate'] ?? 0);
                if (blank($line['description'] ?? null) || $quantity <= 0 || $unit < 0 || $discountRate < 0 || $discountRate > 100 || $taxRate < 0) {
                    throw new InvalidInvoice('Invoice lines must have valid descriptions, quantities, prices, discounts, and tax rates.');
                }$gross = round($quantity * $unit, 2);
                $lineDiscount = round($gross * $discountRate / 100, 2);
                $net = round($gross - $lineDiscount, 2);
                $lineTax = round($net * $taxRate / 100, 2);
                $subtotal += $gross;
                $discount += $lineDiscount;
                $tax += $lineTax;
                $normalized[] = $line + ['discount_rate' => $discountRate, 'tax_rate' => $taxRate, 'net_amount' => $net, 'tax_amount' => $lineTax];
            }$invoice = SalesInvoice::create(array_merge($attributes, ['status' => InvoiceStatus::Draft->value, 'subtotal' => round($subtotal, 2), 'discount_total' => round($discount, 2), 'tax_total' => round($tax, 2), 'total' => round($subtotal - $discount + $tax, 2)]));
            $invoice->lines()->createMany($normalized);
            DB::afterCommit(fn () => event(new InvoiceCreated($invoice->fresh('lines'))));

            return $invoice->load('lines');
        });
    }
}

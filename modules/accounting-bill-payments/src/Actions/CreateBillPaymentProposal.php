<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class CreateBillPaymentProposal
{
    public function handle(array $attributes): BillPaymentProposal
    {
        $amount = (float) ($attributes['amount'] ?? 0);
        $currency = strtoupper((string) ($attributes['currency'] ?? ''));
        $dueDate = $attributes['due_date'] ?? null;
        $bankDetails = $attributes['bank_details'] ?? [];
        if (blank($attributes['supplier_id'] ?? null) || blank($attributes['bill_reference'] ?? null) || $amount <= 0 || strlen($currency) !== 3 || blank($dueDate) || ! is_array($bankDetails) || blank($bankDetails['beneficiary_name'] ?? null) || (blank($bankDetails['iban'] ?? null) && blank($bankDetails['account_number'] ?? null))) {
            throw new InvalidBillPayment('A payment requires a supplier, bill reference, positive amount, currency, due date, and validated bank details.');
        }

        return DB::transaction(function () use ($attributes, $amount, $currency, $bankDetails): BillPaymentProposal {
            $query = BillPaymentProposal::query()->where('team_id', $attributes['team_id'] ?? null)->where('supplier_id', $attributes['supplier_id'])->where('bill_reference', $attributes['bill_reference']);
            if ($query->exists()) {
                throw new InvalidBillPayment('A payment proposal already exists for this supplier bill.');
            }

            return BillPaymentProposal::query()->create(array_merge($attributes, ['amount' => $amount, 'currency' => $currency, 'bank_details' => $bankDetails, 'status' => 'draft']));
        });
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Actions;

use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseItem;

final class AddExpenseItem
{
    public function handle(ExpenseClaim $c, array $a): ExpenseItem
    {
        if ($c->status !== ClaimStatus::Draft) {
            throw new InvalidClaim('Only draft claims can be edited.');
        }foreach (['category_ref', 'spent_on', 'description', 'amount'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidClaim("Missing expense field [{$k}].");
            }
        }$amount = (float) $a['amount'];
        if ($amount <= 0) {
            throw new InvalidClaim('Expense amount must be positive.');
        }$days = $a['per_diem_days'] ?? null;
        if ($days !== null && (float) $days <= 0) {
            throw new InvalidClaim('Per diem days must be positive.');
        }

        return $c->items()->create(['category_ref' => $a['category_ref'], 'spent_on' => $a['spent_on'], 'description' => $a['description'], 'amount' => $amount, 'tax_amount' => $a['tax_amount'] ?? 0, 'merchant' => $a['merchant'] ?? null, 'receipt_ref' => $a['receipt_ref'] ?? null, 'per_diem_days' => $days, 'attendees' => $a['attendees'] ?? null, 'metadata' => $a['metadata'] ?? null]);
    }
}

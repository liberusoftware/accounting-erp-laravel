<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExpenseItem extends Model
{
    protected $table = 'accounting_employee_expense_items';

    protected $fillable = ['claim_id', 'category_ref', 'spent_on', 'description', 'amount', 'tax_amount', 'merchant', 'receipt_ref', 'per_diem_days', 'attendees', 'metadata'];

    protected $casts = ['spent_on' => 'date', 'amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'per_diem_days' => 'decimal:2', 'attendees' => 'array', 'metadata' => 'array'];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(ExpenseClaim::class, 'claim_id');
    }
}

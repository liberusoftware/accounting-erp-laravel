<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Models;

use Illuminate\Database\Eloquent\Model;

final class ExpenseCategory extends Model
{
    protected $table = 'accounting_employee_expense_categories';

    protected $fillable = ['team_id', 'category_ref', 'name', 'daily_limit', 'requires_receipt', 'metadata'];

    protected $casts = ['daily_limit' => 'decimal:2', 'requires_receipt' => 'boolean', 'metadata' => 'array'];
}

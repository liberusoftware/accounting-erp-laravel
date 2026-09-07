<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExpenseHistory extends Model
{
    protected $table = 'accounting_employee_expense_history';

    protected $fillable = ['claim_id', 'event', 'actor_ref', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(ExpenseClaim::class, 'claim_id');
    }
}

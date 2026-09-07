<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;

/** @property ClaimStatus $status @property string $currency @property string $claim_ref */
final class ExpenseClaim extends Model
{
    protected $table = 'accounting_employee_expense_claims';

    protected $fillable = ['team_id', 'employee_ref', 'claim_ref', 'currency', 'status', 'submitted_on', 'approved_on', 'reimbursed_on', 'posted_on', 'rejection_reason', 'project_ref', 'dimension_ref', 'metadata'];

    protected $casts = ['status' => ClaimStatus::class, 'submitted_on' => 'date', 'approved_on' => 'date', 'reimbursed_on' => 'date', 'posted_on' => 'date', 'metadata' => 'array'];

    /** @return HasMany<ExpenseItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class, 'claim_id');
    }

    /** @return HasMany<ExpenseHistory, $this> */
    public function history(): HasMany
    {
        return $this->hasMany(ExpenseHistory::class, 'claim_id');
    }
}

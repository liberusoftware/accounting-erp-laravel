<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Budgets\Enums\BudgetStatus;

final class Budget extends Model
{
    protected $table = 'accounting_budgets';

    protected $fillable = ['team_id', 'name', 'period_start', 'period_end', 'currency', 'status', 'version', 'notes', 'metadata', 'submitted_by', 'approved_by', 'approved_at', 'rejected_at'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'status' => BudgetStatus::class, 'version' => 'integer', 'metadata' => 'array', 'submitted_by' => 'integer', 'approved_by' => 'integer', 'approved_at' => 'datetime', 'rejected_at' => 'datetime'];

    public function lines(): HasMany
    {
        return $this->hasMany(BudgetLine::class);
    }
}

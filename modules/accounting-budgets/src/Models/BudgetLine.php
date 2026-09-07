<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BudgetLine extends Model
{
    protected $table = 'accounting_budget_lines';

    protected $fillable = ['budget_id', 'account_id', 'project_id', 'dimensions', 'planned_amount', 'phases', 'actual_amount', 'notes'];

    protected $casts = ['planned_amount' => 'decimal:2', 'actual_amount' => 'decimal:2', 'dimensions' => 'array', 'phases' => 'array'];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceAllocationType;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceCostStatus;

final class WorkforceCost extends Model
{
    protected $table = 'accounting_workforce_costs';

    protected $fillable = ['team_id', 'worker_ref', 'source_type', 'source_id', 'cost_date', 'hours', 'hourly_rate', 'amount', 'allocation_type', 'allocation_ref', 'capitalized', 'status', 'metadata'];

    protected $casts = ['cost_date' => 'date', 'hours' => 'decimal:6', 'hourly_rate' => 'decimal:6', 'amount' => 'decimal:6', 'allocation_type' => WorkforceAllocationType::class, 'capitalized' => 'boolean', 'status' => WorkforceCostStatus::class, 'metadata' => 'array'];
}

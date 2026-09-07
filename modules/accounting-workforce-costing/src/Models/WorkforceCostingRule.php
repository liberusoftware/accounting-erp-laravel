<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceAllocationType;

final class WorkforceCostingRule extends Model
{
    protected $table = 'accounting_workforce_costing_rules';

    protected $fillable = ['team_id', 'name', 'allocation_type', 'allocation_ref', 'hourly_rate', 'capitalize', 'active', 'metadata'];

    protected $casts = ['allocation_type' => WorkforceAllocationType::class, 'hourly_rate' => 'decimal:6', 'capitalize' => 'boolean', 'active' => 'boolean', 'metadata' => 'array'];
}

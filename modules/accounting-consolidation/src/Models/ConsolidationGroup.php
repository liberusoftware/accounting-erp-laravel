<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Consolidation\Enums\ConsolidationStatus;

final class ConsolidationGroup extends Model
{
    protected $table = 'accounting_consolidation_groups';

    protected $fillable = ['team_id', 'group_ref', 'name', 'reporting_currency', 'status', 'entities', 'eliminations', 'translation', 'report'];

    protected $casts = ['status' => ConsolidationStatus::class, 'entities' => 'array', 'eliminations' => 'array', 'translation' => 'array', 'report' => 'array'];
}

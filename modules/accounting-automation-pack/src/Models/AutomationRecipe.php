<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPack\Models;

use Illuminate\Database\Eloquent\Model;

final class AutomationRecipe extends Model
{
    protected $table = 'accounting_automation_recipes';

    protected $fillable = ['team_id', 'name', 'trigger', 'action', 'schedule', 'status', 'idempotency_key', 'configuration'];

    protected $casts = ['configuration' => 'array'];
}

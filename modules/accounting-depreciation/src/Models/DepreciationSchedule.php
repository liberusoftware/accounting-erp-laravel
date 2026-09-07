<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Depreciation\Enums\DepreciationMethod;
use Liberu\Accounting\Depreciation\Enums\DepreciationScheduleStatus;

final class DepreciationSchedule extends Model
{
    protected $table = 'accounting_depreciation_schedules';

    protected $fillable = ['team_id', 'asset_ref', 'book_ref', 'method', 'convention', 'useful_life_months', 'cost', 'residual_value', 'start_date', 'end_date', 'currency', 'status', 'metadata'];

    protected $casts = ['method' => DepreciationMethod::class, 'status' => DepreciationScheduleStatus::class, 'useful_life_months' => 'integer', 'cost' => 'decimal:2', 'residual_value' => 'decimal:2', 'start_date' => 'date', 'end_date' => 'date', 'metadata' => 'array'];

    public function runs(): HasMany
    {
        return $this->hasMany(DepreciationRun::class, 'schedule_id');
    }
}

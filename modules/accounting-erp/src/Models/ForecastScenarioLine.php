<?php

// src/app/Models/ForecastScenarioLine.php
declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForecastScenarioLine extends Model
{
    use HasFactory;

    #[\Override]
    protected $fillable = ['forecast_scenario_id', 'account_type', 'adjustment_pct'];

    #[\Override]
    protected $casts = ['adjustment_pct' => 'decimal:2'];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(ForecastScenario::class, 'forecast_scenario_id');
    }
}

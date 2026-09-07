<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccounting\Models;

use Illuminate\Database\Eloquent\Model;

final class CarbonActivity extends Model
{
    protected $table = 'accounting_carbon_activities';

    protected $fillable = ['team_id', 'activity_date', 'scope', 'category', 'description', 'quantity', 'unit', 'emission_factor', 'co2e', 'factor_source', 'evidence', 'is_estimate', 'metadata'];

    protected $casts = ['activity_date' => 'date', 'quantity' => 'decimal:6', 'emission_factor' => 'decimal:8', 'co2e' => 'decimal:8', 'evidence' => 'array', 'is_estimate' => 'boolean', 'metadata' => 'array'];
}

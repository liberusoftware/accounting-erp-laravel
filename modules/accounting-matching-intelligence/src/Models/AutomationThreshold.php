<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

final class AutomationThreshold extends Model
{
    protected $table = 'accounting_matching_thresholds';

    protected $fillable = ['team_id', 'match_type', 'minimum_confidence', 'maximum_amount', 'require_evidence', 'active', 'metadata'];

    protected $casts = ['minimum_confidence' => 'decimal:6', 'maximum_amount' => 'decimal:2', 'require_evidence' => 'boolean', 'active' => 'boolean', 'metadata' => 'array'];
}

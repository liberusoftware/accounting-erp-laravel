<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class KpiCommentary extends Model
{
    protected $table = 'accounting_kpi_commentary';

    protected $fillable = ['goal_id', 'actor_ref', 'body', 'period_ref', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(KpiGoal::class, 'goal_id');
    }
}

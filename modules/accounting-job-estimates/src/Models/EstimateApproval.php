<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\JobEstimates\Enums\DecisionType;

final class EstimateApproval extends Model
{
    protected $table = 'accounting_estimate_approvals';

    protected $fillable = ['estimate_id', 'version_id', 'actor_ref', 'decision', 'comment', 'decided_at', 'metadata'];

    protected $casts = ['decision' => DecisionType::class, 'decided_at' => 'datetime', 'metadata' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(JobEstimate::class, 'estimate_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(EstimateVersion::class, 'version_id');
    }
}

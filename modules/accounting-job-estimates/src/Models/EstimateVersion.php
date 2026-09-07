<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;

final class EstimateVersion extends Model
{
    protected $table = 'accounting_estimate_versions';

    protected $fillable = ['estimate_id', 'version_no', 'status', 'notes', 'total_cost', 'total_revenue', 'metadata'];

    protected $casts = ['version_no' => 'integer', 'status' => EstimateStatus::class, 'total_cost' => 'decimal:2', 'total_revenue' => 'decimal:2', 'metadata' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(JobEstimate::class, 'estimate_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(EstimateLine::class, 'version_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(EstimateApproval::class, 'version_id');
    }
}

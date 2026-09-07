<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;

/**
 * @property EstimateStatus $status
 * @property string $estimate_ref
 * @property string $currency
 * @property string $total_cost
 * @property string $total_revenue
 * @property int $version_no
 */
final class JobEstimate extends Model
{
    protected $table = 'accounting_job_estimates';

    protected $fillable = ['team_id', 'estimate_ref', 'project_ref', 'title', 'currency', 'status', 'version_no', 'total_cost', 'total_revenue', 'metadata'];

    protected $casts = ['status' => EstimateStatus::class, 'version_no' => 'integer', 'total_cost' => 'decimal:2', 'total_revenue' => 'decimal:2', 'metadata' => 'array'];

    public function versions(): HasMany
    {
        return $this->hasMany(EstimateVersion::class, 'estimate_id');
    }

    /** @return HasMany<EstimateLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(EstimateLine::class, 'estimate_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(EstimateApproval::class, 'estimate_id');
    }

    public function actuals(): HasMany
    {
        return $this->hasMany(EstimateActual::class, 'estimate_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitability\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\ProjectProfitability\Enums\ProfitabilityStatus;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

/**
 * @property int $id
 * @property int $project_job_id
 * @property ProfitabilityStatus $status
 * @property float|string $revenue_amount
 * @property float|string $cost_amount
 * @property float|string $estimate_amount
 * @property float|string $billed_amount
 */
final class ProjectProfitability extends Model
{
    protected $table = 'accounting_project_profitability';

    protected $fillable = ['team_id', 'project_job_id', 'period_start', 'period_end', 'currency', 'revenue_amount', 'cost_amount', 'estimate_amount', 'committed_amount', 'actual_amount', 'unbilled_wip_amount', 'billed_amount', 'status', 'dimensions', 'source_links', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'revenue_amount' => 'decimal:2', 'cost_amount' => 'decimal:2', 'estimate_amount' => 'decimal:2', 'committed_amount' => 'decimal:2', 'actual_amount' => 'decimal:2', 'unbilled_wip_amount' => 'decimal:2', 'billed_amount' => 'decimal:2', 'status' => ProfitabilityStatus::class, 'dimensions' => 'array', 'source_links' => 'array', 'metadata' => 'array'];

    /** @return BelongsTo<ProjectJob, $this> */
    public function projectJob(): BelongsTo
    {
        return $this->belongsTo(ProjectJob::class, 'project_job_id');
    }

    public function marginAmount(): float
    {
        return (float) $this->revenue_amount - (float) $this->cost_amount;
    }

    public function marginPercent(): float
    {
        return (float) $this->revenue_amount === 0.0 ? 0.0 : ($this->marginAmount() / (float) $this->revenue_amount) * 100;
    }

    public function realizationPercent(): float
    {
        return (float) $this->estimate_amount === 0.0 ? 0.0 : ((float) $this->billed_amount / (float) $this->estimate_amount) * 100;
    }
}

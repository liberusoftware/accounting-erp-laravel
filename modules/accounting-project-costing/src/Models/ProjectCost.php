<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCosting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\ProjectCosting\Enums\CostType;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

/**
 * @property int $id
 * @property int $project_job_id
 * @property CostType $type
 * @property float|string $amount
 */
final class ProjectCost extends Model
{
    protected $table = 'accounting_project_costs';

    protected $fillable = ['team_id', 'project_job_id', 'type', 'occurred_on', 'amount', 'currency', 'committed', 'actual', 'wip_amount', 'source_ref', 'dimensions', 'metadata'];

    protected $casts = ['type' => CostType::class, 'occurred_on' => 'date', 'amount' => 'decimal:2', 'committed' => 'boolean', 'actual' => 'boolean', 'wip_amount' => 'decimal:2', 'dimensions' => 'array', 'metadata' => 'array'];

    /** @return BelongsTo<ProjectJob, $this> */
    public function projectJob(): BelongsTo
    {
        return $this->belongsTo(ProjectJob::class, 'project_job_id');
    }
}

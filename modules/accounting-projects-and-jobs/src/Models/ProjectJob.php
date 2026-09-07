<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\ProjectsAndJobs\Enums\ProjectStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property int|null $customer_id
 * @property int|null $parent_id
 * @property string $name
 * @property string|null $code
 * @property ProjectStatus $status
 * @property float|string|null $budget_amount
 * @property array<string,mixed>|null $dimensions
 */
final class ProjectJob extends Model
{
    protected $table = 'accounting_projects_and_jobs';

    protected $fillable = ['team_id', 'customer_id', 'parent_id', 'name', 'code', 'description', 'start_date', 'end_date', 'status', 'manager_ref', 'budget_amount', 'budget_currency', 'dimensions', 'source_links', 'metadata'];

    protected $casts = ['status' => ProjectStatus::class, 'start_date' => 'date', 'end_date' => 'date', 'budget_amount' => 'decimal:2', 'dimensions' => 'array', 'source_links' => 'array', 'metadata' => 'array'];

    /** @return BelongsTo<ProjectCustomer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(ProjectCustomer::class, 'customer_id');
    }

    /** @return BelongsTo<ProjectJob, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<ProjectJob, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}

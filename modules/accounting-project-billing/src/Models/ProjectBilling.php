<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\ProjectBilling\Enums\BillingMethod;
use Liberu\Accounting\ProjectBilling\Enums\BillingStatus;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

/**
 * @property int $id
 * @property int $project_job_id
 * @property BillingMethod $method
 * @property BillingStatus $status
 * @property float|string $amount
 * @property float|string $billable_time_amount
 * @property float|string $billable_expense_amount
 * @property float|string $write_up_down_amount
 * @property string|null $invoice_ref
 */
final class ProjectBilling extends Model
{
    protected $table = 'accounting_project_billings';

    protected $fillable = ['team_id', 'project_job_id', 'method', 'status', 'period_start', 'period_end', 'currency', 'quantity', 'rate', 'amount', 'progress_percent', 'billable_time_amount', 'billable_expense_amount', 'retainer_amount', 'write_up_down_amount', 'source_ref', 'invoice_ref', 'metadata'];

    protected $casts = ['method' => BillingMethod::class, 'status' => BillingStatus::class, 'period_start' => 'date', 'period_end' => 'date', 'quantity' => 'decimal:4', 'rate' => 'decimal:4', 'amount' => 'decimal:2', 'progress_percent' => 'decimal:2', 'billable_time_amount' => 'decimal:2', 'billable_expense_amount' => 'decimal:2', 'retainer_amount' => 'decimal:2', 'write_up_down_amount' => 'decimal:2', 'metadata' => 'array'];

    /** @return BelongsTo<ProjectJob, $this> */
    public function projectJob(): BelongsTo
    {
        return $this->belongsTo(ProjectJob::class, 'project_job_id');
    }

    public function billableTotal(): float
    {
        return (float) $this->billable_time_amount + (float) $this->billable_expense_amount + (float) $this->write_up_down_amount;
    }
}

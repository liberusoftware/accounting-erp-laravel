<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\OperationalReports\Enums\ReportCategory;
use Liberu\Accounting\OperationalReports\Enums\ReportRunStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string|null $currency
 * @property ReportCategory $category
 * @property ReportRunStatus $status
 * @property array<string,mixed>|null $filters
 * @property array<string,mixed>|null $summary
 */
final class ReportRun extends Model
{
    protected $table = 'accounting_operational_report_runs';

    protected $fillable = ['team_id', 'report_key', 'name', 'category', 'period_start', 'period_end', 'currency', 'status', 'filters', 'summary', 'source_hash', 'requested_by', 'published_by', 'published_at', 'failure_message', 'metadata'];

    protected $casts = ['category' => ReportCategory::class, 'status' => ReportRunStatus::class, 'filters' => 'array', 'summary' => 'array', 'metadata' => 'array', 'period_start' => 'date', 'period_end' => 'date', 'published_at' => 'datetime'];

    public function rows(): HasMany
    {
        return $this->hasMany(ReportRow::class, 'run_id');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ReportException::class, 'run_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(ReportAudit::class, 'run_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\CustomReportBuilder\Enums\ReportExportStatus;

final class CustomReportExport extends Model
{
    protected $table = 'accounting_custom_report_exports';

    protected $fillable = ['report_id', 'team_id', 'format', 'status', 'parameters', 'completed_at'];

    protected $casts = ['status' => ReportExportStatus::class, 'parameters' => 'array', 'completed_at' => 'datetime'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CustomReport::class, 'report_id');
    }
}

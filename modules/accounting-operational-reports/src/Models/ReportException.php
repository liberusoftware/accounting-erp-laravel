<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportException extends Model
{
    protected $table = 'accounting_operational_report_exceptions';

    protected $fillable = ['run_id', 'exception_key', 'severity', 'message', 'source_type', 'source_id', 'status', 'resolution', 'resolved_by', 'resolved_at', 'metadata'];

    protected $casts = ['resolved_at' => 'datetime', 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(ReportRun::class, 'run_id');
    }
}

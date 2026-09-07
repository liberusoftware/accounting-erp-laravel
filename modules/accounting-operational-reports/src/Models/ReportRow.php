<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportRow extends Model
{
    protected $table = 'accounting_operational_report_rows';

    protected $fillable = ['run_id', 'row_key', 'label', 'source_type', 'source_id', 'amount', 'currency', 'state', 'dimensions', 'payload'];

    protected $casts = ['amount' => 'decimal:2', 'dimensions' => 'array', 'payload' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(ReportRun::class, 'run_id');
    }
}

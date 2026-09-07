<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportSchedule extends Model
{
    protected $table = 'accounting_management_report_schedules';

    protected $fillable = ['report_pack_id', 'frequency', 'timezone', 'recipients', 'next_run_at', 'active', 'last_run_at', 'failure_message', 'metadata'];

    protected $casts = ['recipients' => 'array', 'next_run_at' => 'datetime', 'active' => 'boolean', 'last_run_at' => 'datetime', 'metadata' => 'array'];

    public function reportPack(): BelongsTo
    {
        return $this->belongsTo(ReportPack::class, 'report_pack_id');
    }
}

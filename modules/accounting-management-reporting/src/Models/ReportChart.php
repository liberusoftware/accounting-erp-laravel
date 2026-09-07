<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportChart extends Model
{
    protected $table = 'accounting_management_report_charts';

    protected $fillable = ['report_pack_id', 'chart_ref', 'title', 'chart_type', 'data_source', 'series', 'options', 'metadata'];

    protected $casts = ['series' => 'array', 'options' => 'array', 'metadata' => 'array'];

    public function reportPack(): BelongsTo
    {
        return $this->belongsTo(ReportPack::class, 'report_pack_id');
    }
}

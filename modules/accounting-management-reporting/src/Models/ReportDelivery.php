<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportDelivery extends Model
{
    protected $table = 'accounting_management_report_deliveries';

    protected $fillable = ['report_pack_id', 'format', 'file_ref', 'status', 'recipients', 'checksum', 'delivered_at', 'failure_message', 'metadata'];

    protected $casts = ['recipients' => 'array', 'delivered_at' => 'datetime', 'metadata' => 'array'];

    public function reportPack(): BelongsTo
    {
        return $this->belongsTo(ReportPack::class, 'report_pack_id');
    }
}

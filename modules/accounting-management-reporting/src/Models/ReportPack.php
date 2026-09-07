<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\ManagementReporting\Enums\ReportStatus; /** @property ReportStatus $status */
final class ReportPack extends Model
{
    protected $table = 'accounting_management_report_packs';

    protected $fillable = ['team_id', 'report_ref', 'name', 'period_start', 'period_end', 'currency', 'status', 'version', 'owner_ref', 'approved_by', 'approved_at', 'delivered_at', 'archived_at', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'status' => ReportStatus::class, 'version' => 'integer', 'approved_at' => 'datetime', 'delivered_at' => 'datetime', 'archived_at' => 'datetime', 'metadata' => 'array'];

    public function narratives(): HasMany
    {
        return $this->hasMany(ReportNarrative::class, 'report_pack_id');
    }

    public function charts(): HasMany
    {
        return $this->hasMany(ReportChart::class, 'report_pack_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class, 'report_pack_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ReportReview::class, 'report_pack_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(ReportDelivery::class, 'report_pack_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\ManagementReporting\Enums\ReviewDecision;

final class ReportReview extends Model
{
    protected $table = 'accounting_management_report_reviews';

    protected $fillable = ['report_pack_id', 'actor_ref', 'decision', 'comment', 'reviewed_at', 'metadata'];

    protected $casts = ['decision' => ReviewDecision::class, 'reviewed_at' => 'datetime', 'metadata' => 'array'];

    public function reportPack(): BelongsTo
    {
        return $this->belongsTo(ReportPack::class, 'report_pack_id');
    }
}

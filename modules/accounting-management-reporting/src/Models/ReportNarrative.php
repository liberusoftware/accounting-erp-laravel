<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportNarrative extends Model
{
    protected $table = 'accounting_management_report_narratives';

    protected $fillable = ['report_pack_id', 'section_ref', 'title', 'body', 'author_ref', 'version', 'metadata'];

    protected $casts = ['version' => 'integer', 'metadata' => 'array'];

    public function reportPack(): BelongsTo
    {
        return $this->belongsTo(ReportPack::class, 'report_pack_id');
    }
}

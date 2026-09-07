<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CustomReportVariant extends Model
{
    protected $table = 'accounting_custom_report_variants';

    protected $fillable = ['report_id', 'team_id', 'variant_ref', 'configuration'];

    protected $casts = ['configuration' => 'array'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CustomReport::class, 'report_id');
    }
}

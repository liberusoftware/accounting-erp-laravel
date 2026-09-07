<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EstimateVersion extends Model
{
    protected $table = 'accounting_sales_estimate_versions';

    protected $fillable = ['estimate_id', 'version', 'snapshot', 'created_by'];

    protected $casts = ['version' => 'integer', 'snapshot' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }
}

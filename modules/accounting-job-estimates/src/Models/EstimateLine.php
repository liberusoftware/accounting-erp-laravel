<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\JobEstimates\Enums\LineType;

/**
 * @property string $line_ref
 * @property LineType $line_type
 * @property string $category
 * @property string $description
 * @property string $quantity
 * @property string $rate
 * @property string $amount
 * @property string $actual_amount
 * @property array<string,mixed>|null $metadata
 */
final class EstimateLine extends Model
{
    protected $table = 'accounting_estimate_lines';

    protected $fillable = ['estimate_id', 'version_id', 'line_ref', 'line_type', 'category', 'description', 'quantity', 'rate', 'amount', 'actual_amount', 'metadata'];

    protected $casts = ['line_type' => LineType::class, 'quantity' => 'decimal:4', 'rate' => 'decimal:4', 'amount' => 'decimal:2', 'actual_amount' => 'decimal:2', 'metadata' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(JobEstimate::class, 'estimate_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(EstimateVersion::class, 'version_id');
    }
}

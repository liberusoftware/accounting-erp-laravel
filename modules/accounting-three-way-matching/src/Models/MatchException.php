<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionSeverity;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionStatus;

/**
 * @property int $id
 * @property int $match_id
 * @property string $kind
 * @property ExceptionSeverity $severity
 * @property ExceptionStatus $status
 * @property float|string|null $expected_value
 * @property float|string|null $actual_value
 * @property float|string|null $tolerance
 * @property string|null $resolution
 * @property int|null $resolved_by
 * @property-read MatchRecord $match
 */
final class MatchException extends Model
{
    protected $table = 'accounting_three_way_match_exceptions';

    protected $fillable = ['match_id', 'kind', 'severity', 'status', 'expected_value', 'actual_value', 'tolerance', 'resolution', 'resolved_by', 'resolved_at', 'metadata'];

    protected $casts = ['severity' => ExceptionSeverity::class, 'status' => ExceptionStatus::class, 'expected_value' => 'decimal:4', 'actual_value' => 'decimal:4', 'tolerance' => 'decimal:4', 'resolved_at' => 'datetime', 'metadata' => 'array'];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchRecord::class, 'match_id');
    }
}

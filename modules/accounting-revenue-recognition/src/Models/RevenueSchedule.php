<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;

/**
 * @property int $id
 * @property int $obligation_id
 * @property float|string $total_amount
 * @property string $deferred_account_ref
 * @property string $revenue_account_ref
 * @property RecognitionStatus $status
 * @property bool $funded
 * @property Carbon $start_date
 * @property int $periods
 * @property array<string,mixed>|null $metadata
 */
final class RevenueSchedule extends Model
{
    protected $table = 'accounting_revenue_schedules';

    protected $fillable = ['obligation_id', 'allocation_reference_id', 'total_amount', 'start_date', 'periods', 'deferred_account_ref', 'revenue_account_ref', 'status', 'funded', 'metadata'];

    protected $casts = ['status' => RecognitionStatus::class, 'total_amount' => 'decimal:2', 'start_date' => 'date', 'periods' => 'integer', 'funded' => 'boolean', 'metadata' => 'array'];

    /** @return BelongsTo<RevenueObligation, $this> */
    public function obligation(): BelongsTo
    {
        return $this->belongsTo(RevenueObligation::class, 'obligation_id');
    }

    /** @return HasMany<RevenueScheduleEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(RevenueScheduleEntry::class, 'schedule_id');
    }

    /** @return HasMany<RevenueModification, $this> */
    public function modifications(): HasMany
    {
        return $this->hasMany(RevenueModification::class, 'schedule_id');
    }
}

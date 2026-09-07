<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;

/** @property int $id @property int|null $team_id @property string|null $source_type @property string|null $source_id @property string $currency @property string $status @property int $periods @property float|string $total_amount @property array<string,mixed>|null $metadata */
final class RevenueObligation extends Model
{
    protected $table = 'accounting_revenue_obligations';

    protected $fillable = ['team_id', 'source_type', 'source_id', 'description', 'currency', 'total_amount', 'start_date', 'periods', 'status', 'metadata'];

    protected $casts = ['status' => RecognitionStatus::class, 'total_amount' => 'decimal:2', 'start_date' => 'date', 'periods' => 'integer', 'metadata' => 'array'];

    public function schedules(): HasMany
    {
        return $this->hasMany(RevenueSchedule::class, 'obligation_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;

/**
 * @property int $id
 * @property int $schedule_id
 * @property int $period_number
 * @property float|string $amount
 * @property Carbon $recognition_date
 * @property string|null $ledger_reference
 * @property RecognitionStatus $status
 */
final class RevenueScheduleEntry extends Model
{
    protected $table = 'accounting_revenue_schedule_entries';

    protected $fillable = ['schedule_id', 'period_number', 'recognition_date', 'amount', 'status', 'recognized_at', 'ledger_reference', 'metadata'];

    protected $casts = ['status' => RecognitionStatus::class, 'amount' => 'decimal:2', 'recognition_date' => 'date', 'recognized_at' => 'datetime', 'metadata' => 'array'];
}

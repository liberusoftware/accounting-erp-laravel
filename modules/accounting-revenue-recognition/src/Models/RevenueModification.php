<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $schedule_id @property string $effective_date @property float|string $amount_delta @property string $reason @property string $status */
final class RevenueModification extends Model
{
    protected $table = 'accounting_revenue_modifications';

    protected $fillable = ['schedule_id', 'effective_date', 'amount_delta', 'reason', 'status', 'metadata'];

    protected $casts = ['amount_delta' => 'decimal:2', 'effective_date' => 'date', 'metadata' => 'array'];
}

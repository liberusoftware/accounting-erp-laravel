<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetection\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\AnomalyDetection\Enums\AnomalyStatus;

final class Anomaly extends Model
{
    protected $table = 'accounting_anomalies';

    protected $fillable = ['team_id', 'kind', 'source_type', 'source_id', 'title', 'description', 'confidence', 'status', 'evidence', 'detected_at', 'resolved_at'];

    protected $casts = ['status' => AnomalyStatus::class, 'confidence' => 'decimal:4', 'evidence' => 'array', 'detected_at' => 'datetime', 'resolved_at' => 'datetime'];
}

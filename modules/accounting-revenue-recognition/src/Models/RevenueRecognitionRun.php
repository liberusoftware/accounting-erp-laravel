<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionRunStatus;

/** @property int $id @property int|null $team_id @property string $as_of_date @property RecognitionRunStatus $status @property int $processed_entries @property int $failed_entries @property array<string,mixed>|null $errors */
final class RevenueRecognitionRun extends Model
{
    protected $table = 'accounting_revenue_recognition_runs';

    protected $fillable = ['team_id', 'as_of_date', 'status', 'processed_entries', 'failed_entries', 'errors', 'metadata', 'started_at', 'finished_at'];

    protected $casts = ['status' => RecognitionRunStatus::class, 'as_of_date' => 'date', 'errors' => 'array', 'metadata' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime'];
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\RecurringTransactions\Enums\OccurrenceStatus;

/** @property int $id @property int $template_id @property string $occurrence_on @property string $idempotency_key @property OccurrenceStatus $status @property array<string,mixed>|null $generated_payload @property string|null $error_message */
final class RecurringOccurrence extends Model
{
    protected $table = 'accounting_recurring_transaction_occurrences';

    protected $fillable = ['template_id', 'occurrence_on', 'idempotency_key', 'status', 'generated_payload', 'error_message', 'generated_at', 'metadata'];

    protected $casts = ['occurrence_on' => 'date', 'status' => OccurrenceStatus::class, 'generated_payload' => 'array', 'generated_at' => 'datetime', 'metadata' => 'array'];
}

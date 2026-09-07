<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\RecurringTransactions\Enums\RecurringStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $transaction_type
 * @property string $frequency
 * @property string $next_run_on
 * @property string|null $ends_on
 * @property RecurringStatus $status
 * @property bool $automatic
 * @property array<string,mixed> $date_rules
 * @property array<string,mixed> $amount_rules
 * @property array<string,mixed> $payload
 */
final class RecurringTemplate extends Model
{
    protected $table = 'accounting_recurring_transaction_templates';

    protected $fillable = ['team_id', 'name', 'transaction_type', 'frequency', 'starts_on', 'next_run_on', 'ends_on', 'status', 'automatic', 'date_rules', 'amount_rules', 'payload', 'approved_by', 'approved_at', 'metadata'];

    protected $casts = ['status' => RecurringStatus::class, 'starts_on' => 'date', 'next_run_on' => 'date', 'ends_on' => 'date', 'automatic' => 'boolean', 'date_rules' => 'array', 'amount_rules' => 'array', 'payload' => 'array', 'approved_at' => 'datetime', 'metadata' => 'array'];

    public function occurrences(): HasMany
    {
        return $this->hasMany(RecurringOccurrence::class, 'template_id');
    }
}

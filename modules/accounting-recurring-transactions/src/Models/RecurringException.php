<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $template_id @property int|null $occurrence_id @property string $kind @property string $message @property string $status */
final class RecurringException extends Model
{
    protected $table = 'accounting_recurring_transaction_exceptions';

    protected $fillable = ['template_id', 'occurrence_id', 'kind', 'message', 'status', 'resolved_at', 'metadata'];

    protected $casts = ['resolved_at' => 'datetime', 'metadata' => 'array'];
}

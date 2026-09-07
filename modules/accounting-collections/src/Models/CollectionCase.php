<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Models;

use Illuminate\Database\Eloquent\Model;

final class CollectionCase extends Model
{
    protected $table = 'accounting_collection_cases';

    protected $fillable = ['team_id', 'case_ref', 'customer_ref', 'balance', 'stage', 'interest_rate', 'reminders', 'statement_run', 'promise_to_pay', 'disputes', 'write_off', 'agency'];

    protected $casts = ['balance' => 'decimal:8', 'interest_rate' => 'decimal:4', 'reminders' => 'array', 'statement_run' => 'array', 'promise_to_pay' => 'array', 'disputes' => 'array', 'write_off' => 'array', 'agency' => 'array'];
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BankRuleHistory extends Model
{
    protected $table = 'accounting_bank_rule_history';

    public $timestamps = false;

    protected $fillable = ['team_id', 'rule_id', 'transaction_reference', 'outcome', 'matched', 'actions_applied', 'actor_reference', 'created_at'];

    protected $casts = ['matched' => 'boolean', 'actions_applied' => 'array', 'created_at' => 'datetime'];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(BankRule::class, 'rule_id');
    }
}

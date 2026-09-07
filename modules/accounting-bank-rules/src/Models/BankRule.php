<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\BankRules\Enums\BankRuleAutomationMode;

final class BankRule extends Model
{
    protected $table = 'accounting_bank_rules';

    protected $fillable = ['team_id', 'name', 'priority', 'enabled', 'conditions', 'actions', 'automation_mode', 'metadata'];

    protected $casts = ['priority' => 'integer', 'enabled' => 'boolean', 'conditions' => 'array', 'actions' => 'array', 'automation_mode' => BankRuleAutomationMode::class, 'metadata' => 'array'];

    public function histories(): HasMany
    {
        return $this->hasMany(BankRuleHistory::class, 'rule_id');
    }
}

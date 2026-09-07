<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Actions;

use Liberu\Accounting\BankRules\Models\BankRule;
use Liberu\Accounting\BankRules\Models\BankRuleHistory;

final class RecordBankRuleDecision
{
    public function handle(BankRule $rule, string $transactionReference, bool $matched, string $outcome, array $actionsApplied = [], ?string $actorReference = null): BankRuleHistory
    {
        return $rule->histories()->create(['team_id' => $rule->team_id, 'transaction_reference' => trim($transactionReference), 'outcome' => trim($outcome), 'matched' => $matched, 'actions_applied' => $actionsApplied, 'actor_reference' => $actorReference, 'created_at' => now()]);
    }
}

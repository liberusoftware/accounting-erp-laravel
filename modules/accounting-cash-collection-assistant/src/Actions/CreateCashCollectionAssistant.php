<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Actions;

use Liberu\Accounting\CashCollectionAssistant\Exceptions\InvalidCashCollectionAssistant;
use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;

final class CreateCashCollectionAssistant
{
    public function handle(array $attributes): CashCollectionAssistant
    {
        foreach (['team_id', 'invoice_ref'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCashCollectionAssistant("{$field} is required.");
            }
        }
        $score = (int) ($attributes['risk_score'] ?? 0);
        if ($score < 0 || $score > 100) {
            throw new InvalidCashCollectionAssistant('Risk score must be between 0 and 100.');
        }
        $attributes['risk_level'] = $attributes['risk_level'] ?? ($score >= 75 ? 'high' : ($score >= 40 ? 'medium' : 'normal'));

        return CashCollectionAssistant::create([...$attributes, 'risk_score' => $score]);
    }
}

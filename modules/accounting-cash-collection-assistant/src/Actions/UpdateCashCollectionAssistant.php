<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Actions;

use Liberu\Accounting\CashCollectionAssistant\Exceptions\InvalidCashCollectionAssistant;
use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;

final class UpdateCashCollectionAssistant
{
    public function handle(CashCollectionAssistant $assistant, array $attributes): CashCollectionAssistant
    {
        if (array_key_exists('risk_score', $attributes) && ((int) $attributes['risk_score'] < 0 || (int) $attributes['risk_score'] > 100)) {
            throw new InvalidCashCollectionAssistant('Risk score must be between 0 and 100.');
        }
        $assistant->fill($attributes);
        if (array_key_exists('risk_score', $attributes)) {
            $assistant->risk_level = (int) $attributes['risk_score'] >= 75 ? 'high' : ((int) $attributes['risk_score'] >= 40 ? 'medium' : 'normal');
        } $assistant->save();

        return $assistant->refresh();
    }
}

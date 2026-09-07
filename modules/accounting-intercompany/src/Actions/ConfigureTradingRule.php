<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyCounterparty;
use Liberu\Accounting\Intercompany\Models\TradingRule;

final class ConfigureTradingRule
{
    public function handle(IntercompanyCounterparty $counterparty, array $a): TradingRule
    {
        $markup = (float) ($a['markup_percent'] ?? -1);
        foreach (['rule_ref', 'description', 'pricing_method', 'currency'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidIntercompany("Missing rule field [{$k}].");
            }
        }if ($markup < 0) {
            throw new InvalidIntercompany('Markup cannot be negative.');
        }

        return TradingRule::create(['counterparty_id' => $counterparty->getKey(), 'rule_ref' => $a['rule_ref'], 'description' => $a['description'], 'pricing_method' => $a['pricing_method'], 'markup_percent' => $markup, 'currency' => strtoupper($a['currency']), 'active' => $a['active'] ?? true, 'metadata' => $a['metadata'] ?? null]);
    }
}

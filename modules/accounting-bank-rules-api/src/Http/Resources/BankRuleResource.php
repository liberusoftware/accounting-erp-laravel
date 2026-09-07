<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\BankRules\Models\BankRule;

/** @mixin BankRule */
final class BankRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-bank-rule', 'attributes' => ['name' => $this->name, 'priority' => $this->priority, 'enabled' => $this->enabled, 'conditions' => $this->conditions, 'actions' => $this->actions, 'automation_mode' => $this->automation_mode?->value, 'created_at' => $this->created_at?->toIso8601String(), 'updated_at' => $this->updated_at?->toIso8601String()]];
    }
}

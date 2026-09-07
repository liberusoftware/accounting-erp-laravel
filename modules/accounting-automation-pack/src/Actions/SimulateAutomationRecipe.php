<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPack\Actions;

use Liberu\Accounting\AutomationPack\Models\AutomationRecipe;

final class SimulateAutomationRecipe
{
    public function handle(AutomationRecipe $recipe, array $payload = []): array
    {
        return ['recipe_id' => $recipe->getKey(), 'action' => $recipe->action, 'payload' => $payload, 'would_execute' => $recipe->status === 'active'];
    }
}

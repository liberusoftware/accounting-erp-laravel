<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPack\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AutomationPack\Models\AutomationRecipe;

final class CreateAutomationRecipe
{
    public function handle(array $attributes): AutomationRecipe
    {
        return DB::transaction(fn (): AutomationRecipe => AutomationRecipe::create([
            ...$attributes,
            'status' => $attributes['status'] ?? 'draft',
        ]));
    }
}

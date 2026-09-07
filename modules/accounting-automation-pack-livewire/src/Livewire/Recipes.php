<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPackLivewire\Livewire;

use Illuminate\View\View;
use Liberu\Accounting\AutomationPack\Models\AutomationRecipe;
use Livewire\Component;

final class Recipes extends Component
{
    public function render(): View
    {
        return view('accounting-automation-pack-livewire::recipes', ['recipes' => AutomationRecipe::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->get()]);
    }
}

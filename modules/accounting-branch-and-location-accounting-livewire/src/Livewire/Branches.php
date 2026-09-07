<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingLivewire\Livewire;

use Illuminate\View\View;
use Liberu\Accounting\BranchLocationAccounting\Models\Branch;
use Livewire\Component;

final class Branches extends Component
{
    public function render(): View
    {
        return view('accounting-branch-location-livewire::branches', ['branches' => Branch::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->orderBy('code')->get()]);
    }
}

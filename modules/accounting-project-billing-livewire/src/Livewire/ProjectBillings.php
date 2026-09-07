<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;
use Livewire\Component;
use Livewire\WithPagination;

final class ProjectBillings extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view project billing.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-project-billing::project-billings', ['billings' => ProjectBilling::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('period_start')->paginate(15)]);
    }
}

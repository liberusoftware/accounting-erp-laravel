<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Leases\Queries\LeaseQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Leases extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view leases.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-leases-livewire::leases', ['leases' => app(LeaseQuery::class)->leases(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}

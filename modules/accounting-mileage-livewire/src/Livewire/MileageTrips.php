<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Mileage\Queries\MileageQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class MileageTrips extends Component
{
    use WithPagination;

    public string $region = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view mileage.');
        }
    }

    public function updatedRegion(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-mileage-livewire::mileage-trips', ['trips' => app(MileageQuery::class)->trips(auth()->user()?->current_team_id)]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\MultiEntity\Queries\EntityQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class EntityBooks extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view entity books.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-multi-entity::entity-books', ['entities' => app(EntityQuery::class)->paginate(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}

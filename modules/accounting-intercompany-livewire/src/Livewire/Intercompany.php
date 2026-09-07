<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Queries\IntercompanyQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Intercompany extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view intercompany transactions.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-intercompany-livewire::transactions', ['transactions' => app(IntercompanyQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? TransactionStatus::from($this->status) : null)]);
    }
}

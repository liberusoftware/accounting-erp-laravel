<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Transfers\Models\Transfer;
use Livewire\Component;
use Livewire\WithPagination;

final class TransfersList extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view transfers.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-transfers::list', ['transfers' => Transfer::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}

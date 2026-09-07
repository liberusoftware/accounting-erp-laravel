<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;
use Livewire\Component;
use Livewire\WithPagination;

final class TaxReturns extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view tax returns.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-tax-returns::list', ['returns' => TaxReturn::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('period_end')->paginate(15)]);
    }
}

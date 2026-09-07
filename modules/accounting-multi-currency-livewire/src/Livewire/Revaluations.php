<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\MultiCurrency\Queries\CurrencyQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Revaluations extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view currency revaluations.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-multi-currency::revaluations', ['runs' => app(CurrencyQuery::class)->revaluations(auth()->user()?->current_team_id)]);
    }
}

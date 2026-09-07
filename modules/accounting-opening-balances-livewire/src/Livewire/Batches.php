<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\OpeningBalances\Queries\OpeningBalanceQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Batches extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view opening balances.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-opening-balances::batches', ['batches' => app(OpeningBalanceQuery::class)->paginate(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}

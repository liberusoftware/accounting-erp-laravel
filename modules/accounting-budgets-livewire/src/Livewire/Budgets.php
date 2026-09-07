<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsLivewire\Livewire;

use Liberu\Accounting\Budgets\Queries\BudgetQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Budgets extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function render(): mixed
    {
        return view('accounting-budgets::budgets', ['budgets' => app(BudgetQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->status ?: null)]);
    }
}

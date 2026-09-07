<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\YearEnd\Models\YearEndClose;
use Livewire\Component;
use Livewire\WithPagination;

final class YearEndCloses extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view year-end closes.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-year-end::list', ['closes' => YearEndClose::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('fiscal_year')->paginate(15)]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Workpapers\Models\Workpaper;
use Livewire\Component;
use Livewire\WithPagination;

final class Workpapers extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view workpapers.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-workpapers::list', ['workpapers' => Workpaper::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}

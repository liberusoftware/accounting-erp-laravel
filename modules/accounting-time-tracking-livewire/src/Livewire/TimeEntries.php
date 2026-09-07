<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;
use Livewire\Component;
use Livewire\WithPagination;

final class TimeEntries extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view time entries.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-time-tracking::entries', ['entries' => TimeEntry::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('worked_on')->paginate(15)]);
    }
}

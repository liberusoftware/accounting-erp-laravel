<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;
use Livewire\Component;
use Livewire\WithPagination;

final class RevenueSchedules extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view revenue schedules.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-revenue-recognition::revenue-schedules', ['schedules' => RevenueSchedule::query()->whereHas('obligation', fn ($query) => $query->where('team_id', (int) (auth()->user()?->current_team_id ?? -1)))->latest('start_date')->paginate(15)]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Queries\ForecastQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Forecasts extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view forecasts.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-forecasts-livewire::forecasts', ['forecasts' => app(ForecastQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? ForecastStatus::from($this->status) : null)]);
    }
}

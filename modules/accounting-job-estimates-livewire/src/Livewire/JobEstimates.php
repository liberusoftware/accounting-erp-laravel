<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Queries\JobEstimateQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class JobEstimates extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view job estimates.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-job-estimates-livewire::estimates', ['estimates' => app(JobEstimateQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? EstimateStatus::from($this->status) : null)]);
    }
}

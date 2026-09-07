<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Queries\EstimateQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Estimates extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view estimates.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('accounting-estimates-and-quotes-livewire::estimates', ['estimates' => app(EstimateQuery::class)->paginate(null, $this->status !== '' ? EstimateStatus::tryFrom($this->status) : null)]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\MatchingIntelligence\Queries\MatchingQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class MatchingSuggestions extends Component
{
    use WithPagination;

    public string $status = 'suggested';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view matching suggestions.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-matching-intelligence-livewire::matching-suggestions', ['suggestions' => app(MatchingQuery::class)->suggestions(auth()->user()?->current_team_id, $this->status)]);
    }
}

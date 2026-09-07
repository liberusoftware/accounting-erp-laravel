<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ThreeWayMatching\Queries\MatchQuery;
use Livewire\Component;

final class Matches extends Component
{
    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view matching evidence.');
        }
    }

    public function render(): mixed
    {
        return view('accounting-three-way-matching::matches', ['matches' => app(MatchQuery::class)->paginate($this->status !== '' ? $this->status : null)]);
    }
}

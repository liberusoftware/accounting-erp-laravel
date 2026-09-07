<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\BankFeeds\Queries\BankFeedQuery;
use Livewire\Component;

final class Connections extends Component
{
    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view bank feeds.');
        }
    }

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;

        return view('accounting-bank-feeds::connections', ['connections' => app(BankFeedQuery::class)->connections($teamId)->get()]);
    }
}

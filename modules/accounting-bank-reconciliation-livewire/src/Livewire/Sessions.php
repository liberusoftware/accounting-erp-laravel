<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;
use Liberu\Accounting\BankReconciliation\Queries\ReconciliationSummaryQuery;
use Livewire\Component;

final class Sessions extends Component
{
    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view reconciliations.');
        }
    }

    public function render(): mixed
    {
        $sessions = ReconciliationSession::query()->when(auth()->user()?->current_team_id !== null, fn ($query): mixed => $query->where('team_id', auth()->user()->current_team_id))->latest()->get();
        $summaries = $sessions->mapWithKeys(fn (ReconciliationSession $session): array => [$session->id => app(ReconciliationSummaryQuery::class)->handle($session)]);

        return view('accounting-bank-reconciliation::sessions', compact('sessions', 'summaries'));
    }
}

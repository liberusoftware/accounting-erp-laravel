<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\PaymentReconciliation\Queries\SettlementQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class SettlementRuns extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view payment reconciliations.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-payment-reconciliation::settlement-runs', ['runs' => app(SettlementQuery::class)->paginate(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}

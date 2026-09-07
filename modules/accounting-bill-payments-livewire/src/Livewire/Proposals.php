<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;
use Livewire\Component;

final class Proposals extends Component
{
    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view bill payments.');
        }
    }

    public function render(): mixed
    {
        $payments = BillPaymentProposal::query()->when(auth()->user()?->current_team_id !== null, fn ($query): mixed => $query->where('team_id', auth()->user()->current_team_id))->latest()->get();

        return view('accounting-bill-payments::proposals', compact('payments'));
    }
}

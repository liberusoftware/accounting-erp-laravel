<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPaymentsLivewire\Livewire;

use Liberu\Accounting\CustomerPayments\Queries\CustomerPaymentQuery;
use Livewire\Component;

final class PaymentOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-customer-payments::overview', ['payments' => app(CustomerPaymentQuery::class)->forTeam($teamId)]);
    }
}

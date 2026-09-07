<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\PayrollLiabilities\Actions\AllocatePayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;
use Livewire\Component;

final class PayrollLiabilities extends Component
{
    public int|string $selectedLiabilityId = '';

    public string $amount = '';

    public string $allocationRef = '';

    public function selectLiability(int $liabilityId): void
    {
        $this->selectedLiabilityId = $liabilityId;
    }

    public function allocate(AllocatePayrollLiability $action): void
    {
        $data = $this->validate([
            'selectedLiabilityId' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'allocationRef' => ['required', 'string', 'max:255'],
        ]);
        $action->handle($this->currentLiability(), (float) $data['amount'], $data['allocationRef']);
        $this->reset('selectedLiabilityId', 'amount', 'allocationRef');
        $this->dispatch('payroll-liability-allocated');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-payroll-liabilities-livewire::livewire.payroll-liabilities', ['liabilities' => $this->query()->latest('due_on')->paginate(15)]);
    }

    private function query(): Builder
    {
        return PayrollLiability::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    private function currentLiability(): PayrollLiability
    {
        return $this->query()->findOrFail($this->selectedLiabilityId);
    }
}

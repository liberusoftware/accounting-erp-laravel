<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\PayrollPayments\Actions\TransitionPayrollPayment;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;
use Livewire\Component;

final class PayrollPayments extends Component
{
    public int|string $selectedBatchId = '';

    public string $status = '';

    public function selectBatch(int $batchId): void
    {
        $this->selectedBatchId = $batchId;
    }

    public function transition(TransitionPayrollPayment $transition): void
    {
        $validated = $this->validate([
            'selectedBatchId' => ['required', 'integer'],
            'status' => ['required', 'in:draft,approved,submitted,settled,failed,reconciled'],
        ]);

        $batch = PayrollPaymentBatch::query()
            ->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))
            ->findOrFail($validated['selectedBatchId']);

        $transition->handle($batch, PaymentStatus::from($validated['status']));
        $this->reset('status', 'selectedBatchId');
        $this->dispatch('payroll-payment-transitioned');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-payroll-payments-livewire::livewire.payroll-payments', [
            'batches' => PayrollPaymentBatch::query()
                ->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))
                ->latest()
                ->paginate(15),
        ]);
    }
}

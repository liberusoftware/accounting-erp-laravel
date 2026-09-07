<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\PurchaseRequisitions\Actions\RecordApproval;
use Liberu\Accounting\PurchaseRequisitions\Actions\TransitionRequisition;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;
use Livewire\Component;

final class PurchaseRequisitions extends Component
{
    public int|string $selectedRequisitionId = '';

    public string $status = '';

    public string $approverRef = '';

    public string $decision = '';

    public string $reason = '';

    public function selectRequisition(int $requisitionId): void
    {
        $this->selectedRequisitionId = $requisitionId;
    }

    public function transition(TransitionRequisition $action): void
    {
        $data = $this->validate(['selectedRequisitionId' => ['required', 'integer'], 'status' => ['required', 'in:draft,submitted,approved,sourcing,converted,rejected,cancelled']]);
        $action->handle($this->query()->findOrFail($data['selectedRequisitionId']), RequisitionStatus::from($data['status']));
        $this->reset('selectedRequisitionId', 'status');
        $this->dispatch('purchase-requisition-transitioned');
    }

    public function approve(RecordApproval $action): void
    {
        $data = $this->validate(['selectedRequisitionId' => ['required', 'integer'], 'approverRef' => ['required', 'string', 'max:190'], 'decision' => ['required', 'in:approved,rejected'], 'reason' => ['nullable', 'string', 'max:1000']]);
        $action->handle($this->query()->findOrFail($data['selectedRequisitionId']), ['approver_ref' => $data['approverRef'], 'decision' => $data['decision'], 'reason' => $data['reason']]);
        $this->reset('selectedRequisitionId', 'approverRef', 'decision', 'reason');
        $this->dispatch('purchase-requisition-approved');
    }

    public function render(): View
    {
        return ViewFacade::make('module-accounting-purchase-requisitions::livewire.purchase-requisitions', ['requisitions' => $this->query()->latest()->paginate(15), 'statuses' => RequisitionStatus::cases()]);
    }

    private function query(): Builder
    {
        return PurchaseRequisition::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }
}

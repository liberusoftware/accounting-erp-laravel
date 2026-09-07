<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\PurchaseOrders\Actions\TransitionPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;
use Livewire\Component;

final class PurchaseOrders extends Component
{
    public int|string $selectedOrderId = '';

    public string $status = '';

    public function selectOrder(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
    }

    public function transition(TransitionPurchaseOrder $action): void
    {
        $data = $this->validate(['selectedOrderId' => ['required', 'integer'], 'status' => ['required', 'in:draft,pending_approval,approved,issued,partially_received,received,closed,cancelled']]);
        $action->handle($this->query()->findOrFail($data['selectedOrderId']), PurchaseOrderStatus::from($data['status']));
        $this->reset('selectedOrderId', 'status');
        $this->dispatch('purchase-order-transitioned');
    }

    public function render(): View
    {
        return ViewFacade::make('module-accounting-purchase-orders::livewire.purchase-orders', ['orders' => $this->query()->latest()->paginate(15), 'statuses' => PurchaseOrderStatus::cases()]);
    }

    private function query(): Builder
    {
        return PurchaseOrder::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }
}

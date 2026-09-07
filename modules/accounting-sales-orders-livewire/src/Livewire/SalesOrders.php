<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersLivewire\Livewire;

use Liberu\Accounting\SalesOrders\Actions\TransitionSalesOrder;
use Liberu\Accounting\SalesOrders\Enums\OrderStatus;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;
use Liberu\Accounting\SalesOrders\Queries\SalesOrderQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class SalesOrders extends Component
{
    use WithPagination;

    public string $status = '';

    public int|string $selectedOrderId = '';

    public function selectOrder(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
    }

    public function transition(TransitionSalesOrder $action): void
    {
        $data = $this->validate(['selectedOrderId' => ['required', 'integer'], 'status' => ['required', 'in:draft,confirmed,partially_invoiced,invoiced,cancelled']]);
        $action->handle(SalesOrder::query()->where('team_id', (int) auth()->user()->current_team_id)->findOrFail($data['selectedOrderId']), OrderStatus::from($data['status']));
        $this->reset('selectedOrderId', 'status');
        $this->dispatch('sales-order-transitioned');
    }

    public function render(): mixed
    {
        abort_unless(auth()->check(), 403);

        return view('module-accounting-sales-orders::orders', ['orders' => app(SalesOrderQuery::class)->paginate(null, $this->status ?: null, 25, (int) auth()->user()->current_team_id)]);
    }
}

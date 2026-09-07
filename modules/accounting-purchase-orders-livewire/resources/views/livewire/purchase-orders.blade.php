<div>
    <h2>Purchase orders</h2>
    <ul>
        @forelse ($orders as $order)
            <li wire:key="order-{{ $order->id }}">
                <button type="button" wire:click="selectOrder({{ $order->id }})">{{ $order->order_number }}</button>
                <span>{{ $order->supplier_ref }} · {{ $order->status->value }} · {{ $order->total_amount }} {{ $order->currency }}</span>
            </li>
        @empty
            <li>No purchase orders found.</li>
        @endforelse
    </ul>
    <form wire:submit="transition">
        <select wire:model="status"><option value="">Select status</option>@foreach ($statuses as $statusOption)<option value="{{ $statusOption->value }}">{{ ucfirst(str_replace('_', ' ', $statusOption->value)) }}</option>@endforeach</select>
        <button type="submit">Change selected order</button>
    </form>
    {{ $orders->links() }}
</div>

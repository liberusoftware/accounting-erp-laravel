<div>
    <h2>Items and services</h2>
    <input wire:model.live="search" placeholder="Search code or name">
    <ul>
        @forelse ($items as $item)
            <li wire:key="item-{{ $item->id }}">{{ $item->code }} — {{ $item->name }} · {{ $item->kind->value }} · {{ $item->sales_price }} {{ $item->currency }}</li>
        @empty
            <li>No active items found.</li>
        @endforelse
    </ul>
    {{ $items->links() }}
</div>

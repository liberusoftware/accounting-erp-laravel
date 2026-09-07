<div>
    <div wire:loading aria-live="polite">Loading supplier bills…</div>
    <label>Search <input wire:model.live="search" type="search" /></label>
    @forelse ($bills as $bill)
        <article wire:key="supplier-bill-{{ $bill->id }}">
            <span>{{ $bill->bill_number }}</span>
            <span>{{ $bill->party?->name }}</span>
            <span>{{ number_format($bill->total, 2) }} {{ $bill->currency }}</span>
            <span>{{ $bill->status->value }}</span>
        </article>
    @empty
        <p>No supplier bills found.</p>
    @endforelse
    {{ $bills->links() }}
</div>

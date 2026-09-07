<div>
    <h1>Payables</h1>
    @forelse ($ledger['open_items'] as $item)
        <article wire:key="payable-{{ $item->id }}">
            <span>{{ $item->reference }}</span>
            <span>{{ number_format($item->outstanding(), 2) }} {{ $item->currency }}</span>
            <span>{{ $item->status->value }}</span>
        </article>
    @empty
        <p>No open payables.</p>
    @endforelse
</div>

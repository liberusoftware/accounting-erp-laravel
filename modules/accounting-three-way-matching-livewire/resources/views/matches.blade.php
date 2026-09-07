<div>
    <div wire:loading aria-live="polite">Loading matches…</div>
    <select wire:model.live="status"><option value="">All statuses</option><option value="matched">Matched</option><option value="partial">Partial</option><option value="exception">Exception</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select>
    @forelse ($matches as $match)
        <article wire:key="match-{{ $match->id }}"><span>{{ $match->purchase_order_id }}</span><span>{{ $match->receipt_id }}</span><span>{{ $match->bill_id }}</span><span>{{ $match->status->value }}</span></article>
    @empty
        <p>No matching records found.</p>
    @endforelse
    {{ $matches->links() }}
</div>

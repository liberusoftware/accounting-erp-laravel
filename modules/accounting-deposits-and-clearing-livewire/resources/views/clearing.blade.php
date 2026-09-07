<div>
    <h2>Undeposited funds</h2>
    <ul>
        @forelse ($funds as $fund)
            <li>{{ $fund->source_type }}:{{ $fund->source_id }} — {{ number_format((float) $fund->amount, 2) }} {{ $fund->currency }}</li>
        @empty
            <li>No undeposited funds.</li>
        @endforelse
    </ul>
</div>

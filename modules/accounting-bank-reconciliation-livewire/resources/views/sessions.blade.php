<section aria-labelledby="reconciliation-heading">
    <h2 id="reconciliation-heading">Bank reconciliation</h2>
    @forelse ($sessions as $session)
        <article wire:key="reconciliation-{{ $session->id }}"><strong>{{ $session->period_start?->toDateString() }} – {{ $session->period_end?->toDateString() }}</strong> <span>{{ $session->status?->value }}</span> <span>Variance: {{ $summaries[$session->id]['variance'] }}</span></article>
    @empty
        <p>No reconciliation sessions are configured.</p>
    @endforelse
</section>

<section aria-labelledby="cash-coding-heading">
    <h2 id="cash-coding-heading">Cash coding</h2>
    @forelse ($batches as $batch)
        <article wire:key="cash-coding-batch-{{ $batch->id }}"><strong>{{ $batch->reference }}</strong> <span>{{ $batch->currency }} {{ $batch->total_amount }}</span> <span>{{ $batch->status?->value }}</span></article>
    @empty
        <p>No cash coding batches.</p>
    @endforelse
</section>

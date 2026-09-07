<section aria-labelledby="bank-feeds-heading">
    <h2 id="bank-feeds-heading">Bank feeds</h2>
    @forelse ($connections as $connection)
        <article wire:key="bank-feed-{{ $connection->id }}"><strong>{{ $connection->name }}</strong> <span>{{ $connection->provider }}</span> <span>{{ $connection->status?->value }}</span></article>
    @empty
        <p>No bank feed connections are configured.</p>
    @endforelse
</section>

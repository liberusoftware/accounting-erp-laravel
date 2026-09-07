<div>
    <h2>Bank accounts</h2>
    <p>Total balance: {{ number_format($summary['total'], 2) }}</p>
    @forelse ($summary['accounts'] as $account)
        <article wire:key="bank-account-{{ $account->id }}">
            <span>{{ $account->name }}</span>
            <span>{{ number_format((float) $account->current_balance, 2) }} {{ $account->currency }}</span>
            <span>{{ $account->status->value }}</span>
        </article>
    @empty
        <p>No bank accounts.</p>
    @endforelse
</div>

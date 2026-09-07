<div>
    <h1>{{ __('Transfers') }}</h1>
    <ul>
        @forelse ($transfers as $transfer)
            <li>{{ $transfer->source_account_ref }} → {{ $transfer->destination_account_ref }}: {{ $transfer->destination_amount }} ({{ $transfer->status?->value }})</li>
        @empty
            <li>{{ __('No transfers found.') }}</li>
        @endforelse
    </ul>
    {{ $transfers->links() }}
</div>

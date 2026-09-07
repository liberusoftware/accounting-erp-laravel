<div>
    <h1>{{ __('Xero connections') }}</h1>
    <ul>
        @forelse ($connections as $connection)
            <li>{{ $connection->tenant_ref }} — {{ $connection->status?->value }}</li>
        @empty
            <li>{{ __('No Xero connections found.') }}</li>
        @endforelse
    </ul>
    {{ $connections->links() }}
</div>

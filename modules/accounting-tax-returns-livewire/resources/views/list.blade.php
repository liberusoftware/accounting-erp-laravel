<div>
    <h1>{{ __('Tax returns') }}</h1>
    <ul>
        @forelse ($returns as $taxReturn)
            <li>{{ $taxReturn->tax_type }} — {{ $taxReturn->jurisdiction }} — {{ $taxReturn->period_end?->format('Y-m-d') }} ({{ $taxReturn->status?->value }})</li>
        @empty
            <li>{{ __('No tax returns found.') }}</li>
        @endforelse
    </ul>
    {{ $returns->links() }}
</div>

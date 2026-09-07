<div>
    <h1>{{ __('Year-end closes') }}</h1>
    <ul>
        @forelse ($closes as $close)
            <li>{{ $close->fiscal_year }} — {{ $close->period_end?->format('Y-m-d') }} — {{ $close->status?->value }}</li>
        @empty
            <li>{{ __('No year-end closes found.') }}</li>
        @endforelse
    </ul>
    {{ $closes->links() }}
</div>

<div>
    <h1>{{ __('Workforce costs') }}</h1>
    <ul>
        @forelse ($costs as $cost)
            <li>{{ $cost->worker_ref }} — {{ $cost->amount }} — {{ $cost->allocation_ref }}</li>
        @empty
            <li>{{ __('No workforce costs found.') }}</li>
        @endforelse
    </ul>
    {{ $costs->links() }}
</div>

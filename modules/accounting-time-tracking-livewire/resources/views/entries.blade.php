<div>
    <h1>{{ __('Time entries') }}</h1>
    <ul>
        @forelse ($entries as $entry)
            <li>{{ $entry->worked_on?->format('Y-m-d') }} — {{ $entry->worker_ref }} — {{ $entry->hours }}h ({{ $entry->status?->value }})</li>
        @empty
            <li>{{ __('No time entries found.') }}</li>
        @endforelse
    </ul>
    {{ $entries->links() }}
</div>

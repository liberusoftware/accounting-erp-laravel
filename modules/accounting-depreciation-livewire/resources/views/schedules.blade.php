<div>
    <h2>Depreciation forecast</h2>
    <ul>
        @forelse ($forecast as $schedule)
            <li>{{ $schedule['asset_ref'] }} — {{ number_format($schedule['remaining'], 2) }} remaining</li>
        @empty
            <li>No depreciation schedules.</li>
        @endforelse
    </ul>
</div>

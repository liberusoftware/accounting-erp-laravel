<div>
    <h1>{{ __('Workpapers') }}</h1>
    <ul>
        @forelse ($workpapers as $workpaper)
            <li>{{ $workpaper->title }} — {{ $workpaper->status?->value }}</li>
        @empty
            <li>{{ __('No workpapers found.') }}</li>
        @endforelse
    </ul>
    {{ $workpapers->links() }}
</div>

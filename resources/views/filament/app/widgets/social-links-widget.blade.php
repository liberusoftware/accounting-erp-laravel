<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap gap-4">
            @foreach ($links as $label => $url)
                @if (in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true))
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                @endif
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

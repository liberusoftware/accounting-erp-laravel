<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Your accounting workspace</x-slot>
        <x-slot name="description">Start with your chart of accounts, then manage money in, money out, and your bank connections.</x-slot>
        @if ($workspace = $this->workspace())
            <div class="accounting-workspace-summary">
                <div>
                    <p class="accounting-action-title">{{ $workspace['name'] }}</p>
                    <p class="accounting-action-description">{{ $workspace['completed'] ? 'Workspace preferences saved' : 'Workspace setup pending' }}</p>
                </div>
                <dl class="flex flex-wrap gap-6 text-sm">
                    <div>
                        <dt class="accounting-action-description">Base currency</dt>
                        <dd class="accounting-action-title">{{ $workspace['currency'] }}</dd>
                    </div>
                    <div>
                        <dt class="accounting-action-description">Financial year starts</dt>
                        <dd class="accounting-action-title">{{ $workspace['fiscal_year_start'] }}</dd>
                    </div>
                </dl>
            </div>
        @endif
        <div class="accounting-action-grid">
            @forelse ($this->actions() as $action)
                <a wire:key="action-{{ $action['label'] }}" href="{{ $action['url'] }}" class="accounting-action-card">
                    <x-filament::icon :icon="$action['icon']" class="h-6 w-6 text-primary-500" />
                    <span class="accounting-action-title">{{ $action['label'] }}</span>
                    <span class="accounting-action-description">{{ $action['description'] }}</span>
                </a>
            @empty
                <p class="text-sm text-gray-600 dark:text-gray-400">Ask your workspace owner to grant access to your accounting tasks.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

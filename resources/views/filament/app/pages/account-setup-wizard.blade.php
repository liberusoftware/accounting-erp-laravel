<x-filament-panels::page>
    @if ((\Filament\Facades\Filament::getTenant() ?? auth()->user()?->currentTeam)?->accounting_setup_completed_at)
        <x-filament::section icon="heroicon-o-check-badge">
            <x-slot name="heading">Your workspace is already configured</x-slot>
            <x-slot name="description">Review or update your preferences below. Existing credentials stay saved when you leave their fields blank.</x-slot>
        </x-filament::section>
    @endif
    <div class="mb-6 grid gap-3 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                <x-filament::icon icon="heroicon-o-building-office" class="h-5 w-5 text-primary-500" />
                Your business &amp; books
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Set your company identity, currency and reporting year.</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                <x-filament::icon icon="heroicon-o-link" class="h-5 w-5 text-primary-500" />
                Optional connections
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Optional: connect services now, or return when your books are ready.</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                <x-filament::icon icon="heroicon-o-check-badge" class="h-5 w-5 text-primary-500" />
                Start with confidence
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Credentials are encrypted and blank fields never overwrite saved values.</p>
        </div>
    </div>

    <x-filament::section>
        <x-slot name="description">
            Configure your company and accounting preferences, then add only the connections you plan to use. You can return here any time from Workspace &amp; Integrations.
        </x-slot>

        @if ($this->configuredIntegrations !== [])
            <div class="mb-6 rounded-lg bg-success-50 p-4 text-sm text-success-800 dark:bg-success-950 dark:text-success-200">
                Credentials are already saved for: {{ collect($this->configuredIntegrations)->map(fn (string $provider): string => strtoupper($provider))->join(', ', ' and ') }}.
                Leave a credential field blank to keep it unchanged.
            </div>
        @endif

        {{ $this->content }}
    </x-filament::section>
</x-filament-panels::page>

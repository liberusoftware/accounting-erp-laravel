<x-filament-panels::page>
    <div class="mb-6 grid gap-3 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                <x-filament::icon icon="heroicon-o-building-office" class="h-5 w-5 text-primary-500" />
                Business profile
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Set your company identity, currency and reporting year.</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                <x-filament::icon icon="heroicon-o-link" class="h-5 w-5 text-primary-500" />
                Connect services
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Add only the OAuth credentials and API keys your team needs.</p>
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
                Connected credentials are already saved for: {{ collect($this->configuredIntegrations)->map(fn (string $provider): string => strtoupper($provider))->join(', ', ' and ') }}.
                Leave a credential field blank to keep it unchanged.
            </div>
        @endif

        {{ $this->content }}
    </x-filament::section>
</x-filament-panels::page>

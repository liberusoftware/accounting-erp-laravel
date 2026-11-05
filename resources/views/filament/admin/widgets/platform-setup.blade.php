<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Configure your accounting platform</x-slot>
        <x-slot name="description">Review platform identity, support contact details, and public display preferences. Business owners configure their own books from the app panel.</x-slot>
        <x-filament::button tag="a" :href="\App\Filament\Admin\Pages\PlatformSetupWizard::getUrl()" icon="heroicon-o-sparkles">
            Open platform setup
        </x-filament::button>
    </x-filament::section>
</x-filament-widgets::widget>

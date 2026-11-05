<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Profile Information</x-slot>
        <form wire:submit="submit">
            {{ $this->form }}
            <x-filament::button type="submit" class="mt-4">Save</x-filament::button>
        </form>
    </x-filament::section>
</x-filament-panels::page>

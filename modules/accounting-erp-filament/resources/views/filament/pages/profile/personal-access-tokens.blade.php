<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Personal Access Tokens</x-slot>
        <p class="text-sm text-gray-500">Manage API tokens for this account.</p>
        <ul class="mt-4 space-y-2">
            @foreach ($user->tokens as $token)
                <li class="flex justify-between">
                    <span>{{ $token->name }}</span>
                    <x-filament::button size="sm" color="danger" wire:click="deleteApiToken('{{ $token->name }}')">Delete</x-filament::button>
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-panels::page>

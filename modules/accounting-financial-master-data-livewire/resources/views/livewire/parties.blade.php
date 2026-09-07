<div>
    <form wire:submit="save" aria-label="Create customer or supplier">
        <label for="master-entity">Legal entity</label><input id="master-entity" type="number" wire:model="legalEntityId">
        @error('legalEntityId') <span role="alert">{{ $message }}</span> @enderror
        <label for="master-type">Type</label><select id="master-type" wire:model="type"><option value="customer">Customer</option><option value="supplier">Supplier</option></select>
        <label for="master-name">Name</label><input id="master-name" type="text" wire:model="name">
        @error('name') <span role="alert">{{ $message }}</span> @enderror
        <label for="master-email">Email</label><input id="master-email" type="email" wire:model="email">
        <button type="submit" wire:loading.attr="disabled">Create record</button>
    </form>
    <ul aria-label="Customers and suppliers">
        @forelse ($parties as $party)<li wire:key="master-party-{{ $party->id }}">{{ $party->name }} ({{ $party->type->value }})</li>@empty<li>No records yet.</li>@endforelse
    </ul>
    {{ $parties->links() }}
</div>

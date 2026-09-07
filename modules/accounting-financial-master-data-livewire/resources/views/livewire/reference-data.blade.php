<div>
    <form wire:submit="save" aria-label="Create reference data">
        <label for="reference-resource">Resource</label><select id="reference-resource" wire:model.live="resource"><option value="items-services">Items/services</option><option value="tax-profiles">Tax profiles</option><option value="payment-terms">Payment terms</option></select>
        <label for="reference-entity">Legal entity</label><input id="reference-entity" type="number" wire:model="legalEntityId">
        <label for="reference-code">Code/SKU</label><input id="reference-code" type="text" wire:model="code">
        <label for="reference-name">Name</label><input id="reference-name" type="text" wire:model="name">
        <button type="submit" wire:loading.attr="disabled">Create reference data</button>
    </form>
    <ul aria-label="Reference data">@forelse ($records as $record)<li wire:key="reference-data-{{ $record->id }}">{{ $record->name }}</li>@empty<li>No reference data yet.</li>@endforelse</ul>
    {{ $records->links() }}
</div>

<div>
    <h2>Payroll liabilities</h2>
    <ul>
        @forelse ($liabilities as $liability)
            <li wire:key="liability-{{ $liability->id }}">
                <button type="button" wire:click="selectLiability({{ $liability->id }})">{{ $liability->liability_ref }}</button>
                <span>{{ $liability->status->value }} · {{ $liability->outstanding() }} {{ $liability->currency }}</span>
            </li>
        @empty
            <li>No payroll liabilities found.</li>
        @endforelse
    </ul>
    <form wire:submit="allocate">
        <input wire:model="amount" type="number" min="0.01" step="0.01" placeholder="Amount">
        <input wire:model="allocationRef" placeholder="Allocation reference">
        <button type="submit">Allocate payment</button>
    </form>
    {{ $liabilities->links() }}
</div>

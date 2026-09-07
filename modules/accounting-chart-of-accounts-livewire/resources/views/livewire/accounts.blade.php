<div>
    <form wire:submit="save" aria-label="Create account">
        <label for="chart-entity">Legal entity</label>
        <input id="chart-entity" type="number" wire:model="legalEntityId">
        @error('legalEntityId') <span role="alert">{{ $message }}</span> @enderror

        <label for="chart-code">Code</label>
        <input id="chart-code" type="text" wire:model="code">
        @error('code') <span role="alert">{{ $message }}</span> @enderror

        <label for="chart-name">Name</label>
        <input id="chart-name" type="text" wire:model="name">
        @error('name') <span role="alert">{{ $message }}</span> @enderror

        <label for="chart-type">Type</label>
        <select id="chart-type" wire:model="type">
            <option value="asset">Asset</option>
            <option value="liability">Liability</option>
            <option value="equity">Equity</option>
            <option value="revenue">Revenue</option>
            <option value="expense">Expense</option>
        </select>
        <button type="submit" wire:loading.attr="disabled">Create account</button>
    </form>

    <ul aria-label="Accounts">
        @forelse ($accounts as $account)
            <li wire:key="chart-account-{{ $account->id }}">{{ $account->code }} — {{ $account->name }}</li>
        @empty
            <li>No accounts yet.</li>
        @endforelse
    </ul>

    {{ $accounts->links() }}
</div>

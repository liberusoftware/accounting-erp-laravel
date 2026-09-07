<div>
    <h2>Payroll imports</h2>
    <form wire:submit="import">
        <input wire:model="provider" placeholder="Provider">
        <input wire:model="periodStart" type="date">
        <input wire:model="periodEnd" type="date">
        <input wire:model="runRef" placeholder="Run reference">
        <input wire:model="currency" maxlength="3">
        <input wire:model="employeeRefs" placeholder="Employee refs, comma separated">
        <button type="submit">Import payroll run</button>
    </form>
    <ul>
        @forelse ($imports as $import)
            <li wire:key="import-{{ $import->id }}">
                <button type="button" wire:click="selectImport({{ $import->id }})">{{ $import->provider }} / {{ $import->run_ref }}</button>
                <span>{{ $import->status->value }} · {{ $import->period_start->toDateString() }}—{{ $import->period_end->toDateString() }}</span>
            </li>
        @empty
            <li>No payroll imports found.</li>
        @endforelse
    </ul>
    <form wire:submit="mark">
        <select wire:model="status"><option value="">Select status</option>@foreach ($statuses as $statusOption)<option value="{{ $statusOption->value }}">{{ ucfirst($statusOption->value) }}</option>@endforeach</select>
        <button type="submit">Update selected import</button>
    </form>
    {{ $imports->links() }}
</div>

<div>
    <form wire:submit="transition">
        <input type="hidden" wire:model="selectedBatchId">
        <label for="payroll-payment-status">New status</label>
        <select id="payroll-payment-status" wire:model="status">
            <option value="">Select status</option>
            <option value="approved">Approved</option>
            <option value="submitted">Submitted</option>
            <option value="settled">Settled</option>
            <option value="failed">Failed</option>
            <option value="reconciled">Reconciled</option>
        </select>
        @error('status') <span role="alert">{{ $message }}</span> @enderror
        <button type="submit" wire:loading.attr="disabled">Transition</button>
    </form>

    <ul aria-label="Payroll payment batches">
        @forelse ($batches as $batch)
            <li wire:key="payroll-payment-{{ $batch->id }}">
                {{ $batch->batch_ref }} — {{ $batch->status->value }}
                <button type="button" wire:click="selectBatch({{ $batch->id }})">Select</button>
            </li>
        @empty
            <li>No payroll payment batches yet.</li>
        @endforelse
    </ul>

    {{ $batches->links() }}
</div>

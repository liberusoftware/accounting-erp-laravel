<div>
    <h2>Purchase requisitions</h2>
    <ul>
        @forelse ($requisitions as $requisition)
            <li wire:key="requisition-{{ $requisition->id }}">
                <button type="button" wire:click="selectRequisition({{ $requisition->id }})">{{ $requisition->requester_ref }}{{ $requisition->title ? ' — '.$requisition->title : '' }}</button>
                <span>{{ $requisition->status->value }} · {{ $requisition->total_amount }} {{ $requisition->currency }}</span>
            </li>
        @empty
            <li>No purchase requisitions found.</li>
        @endforelse
    </ul>
    <form wire:submit="transition">
        <select wire:model="status"><option value="">Select status</option>@foreach ($statuses as $statusOption)<option value="{{ $statusOption->value }}">{{ ucfirst(str_replace('_', ' ', $statusOption->value)) }}</option>@endforeach</select>
        <button type="submit">Change selected requisition</button>
    </form>
    <form wire:submit="approve">
        <input wire:model="approverRef" placeholder="Approver reference">
        <select wire:model="decision"><option value="">Decision</option><option value="approved">Approve</option><option value="rejected">Reject</option></select>
        <input wire:model="reason" placeholder="Reason">
        <button type="submit">Record approval</button>
    </form>
    {{ $requisitions->links() }}
</div>

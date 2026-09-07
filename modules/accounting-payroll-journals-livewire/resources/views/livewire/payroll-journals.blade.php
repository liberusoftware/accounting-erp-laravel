<div>
    <h2>Payroll journals</h2>
    <ul>
        @forelse ($journals as $journal)
            <li wire:key="journal-{{ $journal->id }}">
                <button type="button" wire:click="selectJournal({{ $journal->id }})">{{ $journal->journal_ref }}</button>
                <span>{{ $journal->status->value }} · {{ $journal->net_pay }} {{ $journal->currency }}</span>
            </li>
        @empty
            <li>No payroll journals found.</li>
        @endforelse
    </ul>
    <form wire:submit="post"><button type="submit">Post selected journal</button></form>
    <form wire:submit="reverse"><input wire:model="reversalRef" placeholder="Reversal reference"><button type="submit">Reverse selected journal</button></form>
    {{ $journals->links() }}
</div>

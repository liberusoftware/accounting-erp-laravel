<div>
    <form wire:submit="save"><input wire:model="bookId" placeholder="Book ID"><input wire:model="startsOn" type="date"><input wire:model="endsOn" type="date"><button type="submit">Create period</button></form>
    <ul>@foreach($periods as $period)<li>{{ $period->starts_on->toDateString() }} – {{ $period->ends_on->toDateString() }} ({{ $period->state->value }})</li>@endforeach</ul>
    {{ $periods->links() }}
</div>

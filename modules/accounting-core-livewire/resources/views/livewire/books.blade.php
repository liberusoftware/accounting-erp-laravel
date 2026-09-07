<div>
    <form wire:submit="save">
        <select wire:model="legalEntityId"><option value="">Select legal entity</option>@foreach($legalEntities as $legalEntity)<option value="{{ $legalEntity->id }}">{{ $legalEntity->name }}</option>@endforeach</select>
        <input wire:model="name" placeholder="Book name">
        <input wire:model="code" placeholder="Code">
        <select wire:model="accountingBasis"><option value="accrual">Accrual</option><option value="cash">Cash</option></select>
        <button type="submit">Save book</button>
    </form>
    <ul>@foreach($books as $book)<li>{{ $book->name }} ({{ $book->code }})</li>@endforeach</ul>
    {{ $books->links() }}
</div>

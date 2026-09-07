<div><h2>Credit notes</h2><ul>@forelse ($notes as $note)<li>{{ $note->credit_ref }} — {{ $note->status->value }}</li>@empty<li>No credit notes.</li>@endforelse</ul></div>

<div><h2>Year end</h2><ul>@forelse ($periods as $period)<li>{{ $period->period_ref }} — {{ $period->status->value }}</li>@empty<li>No year-end periods configured.</li>@endforelse</ul></div>

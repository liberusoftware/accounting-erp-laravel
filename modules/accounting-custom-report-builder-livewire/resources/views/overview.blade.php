<div><h2>Custom reports</h2><ul>@forelse ($reports as $report)<li>{{ $report->name }} — {{ count($report->measures) }} measures</li>@empty<li>No custom reports.</li>@endforelse</ul></div>

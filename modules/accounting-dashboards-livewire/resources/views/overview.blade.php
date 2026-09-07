<div><h2>Dashboards</h2><ul>@forelse ($dashboards as $dashboard)<li>{{ $dashboard->name }} ({{ $dashboard->role ?? 'All roles' }})</li>@empty<li>No dashboards configured.</li>@endforelse</ul></div>

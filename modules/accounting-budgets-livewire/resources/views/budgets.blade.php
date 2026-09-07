<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Budgets</h2>
        <select wire:model.live="status" class="rounded border-gray-300">
            <option value="">All statuses</option><option value="draft">Draft</option><option value="submitted">Submitted</option><option value="approved">Approved</option><option value="revised">Revised</option>
        </select>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left"><thead><tr><th>Name</th><th>Period</th><th>Currency</th><th>Status</th><th>Version</th></tr></thead><tbody>
            @forelse ($budgets as $budget)<tr><td>{{ $budget->name }}</td><td>{{ $budget->period_start?->toDateString() }} — {{ $budget->period_end?->toDateString() }}</td><td>{{ $budget->currency }}</td><td>{{ $budget->status->value }}</td><td>{{ $budget->version }}</td></tr>@empty<tr><td colspan="5">No budgets found.</td></tr>@endforelse
        </tbody></table>
    </div>
    {{ $budgets->links() }}
</div>

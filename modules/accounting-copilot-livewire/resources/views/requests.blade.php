<div class="space-y-4">
    <h2 class="text-lg font-semibold">Copilot requests</h2>
    @forelse ($requests as $request)
        <article class="rounded border p-3"><div class="font-medium">{{ $request->kind }}</div><p>{{ $request->prompt }}</p><div class="text-sm text-gray-500">{{ $request->status }}</div></article>
    @empty
        <p>No Copilot requests found.</p>
    @endforelse
</div>

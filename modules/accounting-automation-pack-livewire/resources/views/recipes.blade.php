<div class="space-y-4">
    <h2 class="text-lg font-semibold">Automation recipes</h2>
    <ul class="divide-y">
        @forelse ($recipes as $recipe)
            <li class="py-2"><span>{{ $recipe->name }}</span> <span class="text-sm text-gray-500">{{ $recipe->status }}</span></li>
        @empty
            <li class="py-2">No automation recipes found.</li>
        @endforelse
    </ul>
</div>

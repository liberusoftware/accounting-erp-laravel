<section aria-labelledby="account-reconciliations-heading">
    <h2 id="account-reconciliations-heading">Account reconciliations</h2>
    <select wire:model.live="status"><option value="">All statuses</option><option value="draft">Draft</option><option value="prepared">Prepared</option><option value="in_review">In review</option><option value="certified">Certified</option><option value="carried_forward">Carried forward</option></select>
    @forelse ($reconciliations as $reconciliation)<article wire:key="account-reconciliation-{{ $reconciliation->id }}">Account {{ $reconciliation->account_id }} · {{ $reconciliation->period_end?->toDateString() }} · {{ $reconciliation->status?->value }}</article>@empty<p>No account reconciliations found.</p>@endforelse
    {{ $reconciliations->links() }}
</section>

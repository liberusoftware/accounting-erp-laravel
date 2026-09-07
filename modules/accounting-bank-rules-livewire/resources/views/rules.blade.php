<section aria-labelledby="bank-rules-heading">
    <h2 id="bank-rules-heading">Bank rules</h2>
    <label for="bank-rule-test-text">Test transaction text</label>
    <input id="bank-rule-test-text" wire:model="transactionText" type="text">
    @if ($testResult !== '')
        <p role="status">{{ $testResult }}</p>
    @endif
    @forelse ($rules as $rule)
        <article wire:key="bank-rule-{{ $rule->id }}">
            <strong>{{ $rule->name }}</strong>
            <span>Priority {{ $rule->priority }}</span>
            <span>{{ $rule->automation_mode?->value }}</span>
            <button type="button" wire:click="testRule({{ $rule->id }})">Test</button>
        </article>
    @empty
        <p>No bank rules.</p>
    @endforelse
</section>

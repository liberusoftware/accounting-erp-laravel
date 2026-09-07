<section aria-labelledby="bill-payments-heading">
    <h2 id="bill-payments-heading">Bill payments</h2>
    @forelse ($payments as $payment)
        <article wire:key="bill-payment-{{ $payment->id }}"><strong>{{ $payment->bill_reference }}</strong> <span>{{ $payment->currency }} {{ $payment->amount }}</span> <span>{{ $payment->status?->value }}</span></article>
    @empty
        <p>No bill payment proposals.</p>
    @endforelse
</section>

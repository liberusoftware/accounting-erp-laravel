<div>
    <h2>Payables aging</h2>
    <dl>
        @foreach ($buckets as $bucket => $amount)
            <div wire:key="payables-aging-{{ $bucket }}"><dt>{{ str_replace('_', '–', $bucket) }}</dt><dd>{{ number_format($amount, 2) }}</dd></div>
        @endforeach
    </dl>
</div>

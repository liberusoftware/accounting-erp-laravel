<div>
    <h1>{{ __('VAT records') }}</h1>
    <ul>
        @forelse ($records as $record)
            <li>{{ $record->direction?->value }} — {{ $record->tax_code }} — {{ $record->tax_amount }}</li>
        @empty
            <li>{{ __('No VAT records found.') }}</li>
        @endforelse
    </ul>
    {{ $records->links() }}
</div>

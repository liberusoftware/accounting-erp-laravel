<div>
    <div class="mb-4">
        <label for="fixed-assets-status" class="sr-only">Filter fixed assets by status</label>
        <select id="fixed-assets-status" wire:model.live="status">
            <option value="">All statuses</option>
            @foreach (\Liberu\Accounting\FixedAssets\Enums\AssetStatus::cases() as $assetStatus)
                <option value="{{ $assetStatus->value }}">{{ ucfirst($assetStatus->value) }}</option>
            @endforeach
        </select>
    </div>

    @if ($assets->isEmpty())
        <p role="status">No fixed assets were found.</p>
    @else
        <table>
            <caption class="sr-only">Fixed assets</caption>
            <thead><tr><th scope="col">Reference</th><th scope="col">Name</th><th scope="col">Cost</th><th scope="col">Net book value</th><th scope="col">Status</th></tr></thead>
            <tbody>
                @foreach ($assets as $asset)
                    <tr wire:key="fixed-asset-{{ $asset->getKey() }}">
                        <td>{{ $asset->asset_ref }}</td>
                        <td>{{ $asset->name }}</td>
                        <td>{{ $asset->cost }}</td>
                        <td>{{ $asset->net_book_value }}</td>
                        <td>{{ ucfirst($asset->status->value) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $assets->links() }}
    @endif
</div>

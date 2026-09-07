<div>
    <form wire:submit="save">
        <select wire:model="bookId"><option value="">Select book</option>@foreach($books as $book)<option value="{{ $book->id }}">{{ $book->name }}</option>@endforeach</select>
        <select wire:model="setting"><option value="defaults">Defaults</option><option value="policies">Policies</option></select>
        <input wire:model="key" placeholder="Setting key">
        <textarea wire:model="value" placeholder='{"value": true}'></textarea>
        <button type="submit">Save setting</button>
    </form>
    <ul>@foreach($settings as $setting)<li>{{ $setting->key }}</li>@endforeach</ul>
    {{ $settings->links() }}
</div>

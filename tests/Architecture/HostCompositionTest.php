<?php

use Illuminate\Database\Eloquent\Relations\Relation;
use Liberu\Accounting\Erp\Models\Invoice;

it('keeps business code and schema out of the host', function () {
    $root = dirname(__DIR__, 2);

    foreach (['Models', 'Services', 'Actions', 'Jobs', 'Notifications', 'Console/Commands'] as $directory) {
        expect(glob($root.'/app/'.$directory.'/*.php') ?: [])->toBeEmpty();
    }

    expect(glob($root.'/database/migrations/*.php') ?: [])->toBeEmpty()
        ->and(glob($root.'/database/factories/*.php') ?: [])->toBeEmpty();
});

it('resolves package factories and preserves existing morph references', function () {
    expect(Invoice::factory()->make(['customer_id' => 1])->getMorphClass())->toBe('App\\Models\\Invoice')
        ->and(Relation::getMorphedModel('App\\Models\\Invoice'))->toBe(Invoice::class)
        ->and(is_a('App\\Models\\Invoice', Invoice::class, true))->toBeTrue();
});

it('loads the extracted schema from its owning module', function () {
    $migration = '2026_07_11_100001_add_journal_entry_id_to_invoices_table';
    $files = app('migrator')->getMigrationFiles(app('migrator')->paths());

    expect($files)->toHaveKey($migration)
        ->and(realpath($files[$migration]))->toContain('/modules/accounting-erp/database/migrations/');
});

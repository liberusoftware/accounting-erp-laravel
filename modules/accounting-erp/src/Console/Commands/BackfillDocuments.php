<?php

// src/app/Console/Commands/BackfillDocuments.php
declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\CreditMemo;
use Liberu\Accounting\Erp\Models\Document;
use Liberu\Accounting\Erp\Models\Estimate;
use Liberu\Accounting\Erp\Models\Invoice;

class BackfillDocuments extends Command
{
    #[\Override]
    protected $signature = 'documents:backfill';

    #[\Override]
    protected $description = 'Register legacy document_path strings as version-1 documents';

    public function handle(): void
    {
        $count = 0;
        foreach ([Invoice::class, Bill::class, Estimate::class, CreditMemo::class] as $modelClass) {
            $modelClass::query()->whereNotNull('document_path')->each(function (Model $owner) use (&$count): void {
                $legacyPath = (string) $owner->document_path;
                // Skip only if THIS legacy path is already registered — not merely if
                // the owner has any document (a later Filament upload must not hide the
                // legacy file from ever being backfilled).
                $alreadyBackfilled = $owner->documents()
                    ->whereHas('versions', fn ($q) => $q->where('path', $legacyPath))
                    ->exists();
                if ($alreadyBackfilled) {
                    return;
                }
                /** @var Document $document */
                $document = $owner->documents()->create([
                    'name' => basename((string) $owner->document_path),
                    'disk' => (string) config('documents.disk', 'local'),
                ]);
                $document->versions()->create([
                    'version_number' => 1,
                    'path' => (string) $owner->document_path,
                    'original_filename' => basename((string) $owner->document_path),
                ]);
                $count++;
            });
        }
        $this->info("Backfilled {$count} document(s).");
    }
}

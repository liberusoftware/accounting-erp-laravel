<?php

// src/app/Console/Commands/PruneDocuments.php
declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Services\DocumentService;

class PruneDocuments extends Command
{
    #[\Override]
    protected $signature = 'documents:prune';

    #[\Override]
    protected $description = 'Delete documents past their retention date and their stored files';

    public function handle(DocumentService $service): void
    {
        $this->info("Pruned {$service->prune()} document(s).");
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationRun;

/** @mixin MigrationRun */
final class MigrationRunResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-spreadsheet-migration-run', 'attributes' => ['template_id' => (string) $this->template_id, 'mode' => $this->mode->value, 'status' => $this->status->value, 'row_count' => $this->row_count, 'source_total' => (float) $this->source_total, 'target_total' => (float) $this->target_total, 'errors' => $this->errors], 'links' => ['self' => url('/api/v1/accounting/spreadsheet-migration/runs/'.$this->id)]];
    }
}

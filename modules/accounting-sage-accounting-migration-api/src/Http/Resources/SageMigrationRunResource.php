<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigrationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRecord;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRun;

/** @mixin SageMigrationRun */
final class SageMigrationRunResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-sage-accounting-migration-run', 'attributes' => ['status' => $this->status->value, 'total_records' => $this->total_records, 'imported_records' => $this->imported_records, 'failed_records' => $this->failed_records, 'metadata' => $this->metadata, 'created_at' => $this->created_at?->toIso8601String(), 'finished_at' => $this->finished_at?->toIso8601String()], 'relationships' => ['records' => ['data' => $this->whenLoaded('records', fn (): array => $this->records->map(fn (SageMigrationRecord $record): array => ['id' => (string) $record->id, 'type' => 'migration-record', 'attributes' => ['entity_type' => $record->entity_type, 'source_id' => $record->source_id, 'status' => $record->status->value]])->all())]]];
    }
}

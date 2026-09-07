<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch; /** @mixin MigrationBatch */
final class MigrationBatchResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-migration-batch', 'attributes' => ['batch_ref' => $this->resource->batch_ref, 'status' => $this->resource->status?->value, 'dry_run' => $this->resource->dry_run, 'total_count' => $this->resource->total_count, 'processed_count' => $this->resource->processed_count, 'success_count' => $this->resource->success_count, 'error_count' => $this->resource->error_count, 'resume_token' => $this->resource->resume_token, 'created_at' => $this->resource->created_at?->toISOString()]];
    }
}

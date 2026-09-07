<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Core\Models\LegalEntity;

final class UpdateLegalEntity
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string, mixed> $attributes */
    public function handle(LegalEntity $entity, array $attributes): LegalEntity
    {
        return DB::transaction(function () use ($entity, $attributes): LegalEntity {
            $entity->fill($attributes);
            $entity->save();

            return $entity->refresh();
        });
    }
}

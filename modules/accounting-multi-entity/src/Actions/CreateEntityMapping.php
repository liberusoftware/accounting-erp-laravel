<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Actions;

use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Models\EntityBook;
use Liberu\Accounting\MultiEntity\Models\EntityMapping;

final class CreateEntityMapping
{
    public function handle(EntityBook $entity, string $type, string $source, string $target): EntityMapping
    {
        if (blank($type) || blank($source) || blank($target)) {
            throw new InvalidEntity('Mapping type, source, and target are required.');
        }

        return EntityMapping::updateOrCreate(['entity_id' => $entity->id, 'mapping_type' => $type, 'source_ref' => $source], ['target_ref' => $target, 'is_active' => true]);
    }
}

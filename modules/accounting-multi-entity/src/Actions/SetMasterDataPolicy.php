<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Actions;

use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Models\EntityBook;
use Liberu\Accounting\MultiEntity\Models\MasterDataPolicy;

final class SetMasterDataPolicy
{
    public function handle(EntityBook $entity, string $key, string $mode, array $configuration = []): MasterDataPolicy
    {
        if (blank($key) || ! in_array($mode, ['shared', 'local', 'override'], true)) {
            throw new InvalidEntity('Policy key and supported mode are required.');
        }

        return MasterDataPolicy::updateOrCreate(['entity_id' => $entity->id, 'policy_key' => $key], ['mode' => $mode, 'configuration' => $configuration]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Models\EntityAccess;
use Liberu\Accounting\MultiEntity\Models\EntityBook;

final class GrantEntityAccess
{
    public function handle(EntityBook $entity, string $userRef, string $role, array $permissions = [], bool $default = false): EntityAccess
    {
        if (blank($userRef) || blank($role)) {
            throw new InvalidEntity('User reference and role are required.');
        }

        return DB::transaction(function () use ($entity, $userRef, $role, $permissions, $default): EntityAccess {
            $access = EntityAccess::updateOrCreate(['entity_id' => $entity->id, 'user_ref' => $userRef], ['role' => $role, 'permissions' => $permissions, 'is_default' => $default]);
            if ($default) {
                EntityAccess::query()->where('user_ref', $userRef)->whereKeyNot($access->id)->update(['is_default' => false]);
            }

            return $access->refresh();
        });
    }
}

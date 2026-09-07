<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MultiEntity\Events\EntitySwitched;
use Liberu\Accounting\MultiEntity\Exceptions\InvalidEntity;
use Liberu\Accounting\MultiEntity\Models\EntityBook;
use Liberu\Accounting\MultiEntity\Models\EntitySwitch;

final class SwitchEntity
{
    public function handle(EntityBook $entity, string $userRef, string $sessionRef): EntitySwitch
    {
        if (blank($userRef) || blank($sessionRef)) {
            throw new InvalidEntity('User and session references are required.');
        }if (! $entity->access()->where('user_ref', $userRef)->exists()) {
            throw new InvalidEntity('The user has no access to this entity.');
        }

        return DB::transaction(function () use ($entity, $userRef, $sessionRef): EntitySwitch {
            $switch = EntitySwitch::create(['entity_id' => $entity->id, 'user_ref' => $userRef, 'session_ref' => $sessionRef, 'switched_at' => now()]);
            DB::afterCommit(fn () => event(new EntitySwitched($switch->refresh())));

            return $switch;
        });
    }
}

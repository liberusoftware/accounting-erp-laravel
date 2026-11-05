<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     */
    public function delete($user): void
    {
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->connectedAccounts->each->delete();
        $user->delete();
    }
}

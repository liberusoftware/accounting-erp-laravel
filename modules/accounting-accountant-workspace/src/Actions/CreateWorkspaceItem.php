<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspace\Actions;

use Liberu\Accounting\AccountantWorkspace\Enums\WorkspaceItemStatus;
use Liberu\Accounting\AccountantWorkspace\Models\WorkspaceItem;

final class CreateWorkspaceItem
{
    public function handle(array $attributes): WorkspaceItem
    {
        foreach (['team_id', 'name'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new \InvalidArgumentException("{$field} is required.");
            }
        }

        return WorkspaceItem::create([...$attributes, 'status' => $attributes['status'] ?? WorkspaceItemStatus::Active]);
    }
}

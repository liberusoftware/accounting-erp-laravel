<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Actions;

use Liberu\Accounting\Dashboards\Exceptions\InvalidDashboard;
use Liberu\Accounting\Dashboards\Models\Dashboard;

final class CreateDashboard
{
    public function handle(array $attributes): Dashboard
    {
        foreach (['team_id', 'dashboard_ref', 'name'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidDashboard("{$field} is required.");
            }
        }

        return Dashboard::create([...$attributes, 'period' => $attributes['period'] ?? 'current']);
    }
}

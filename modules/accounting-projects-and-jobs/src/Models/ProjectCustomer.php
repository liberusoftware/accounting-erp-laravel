<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $id @property int|null $team_id @property string $customer_ref @property string $name @property string|null $status */
final class ProjectCustomer extends Model
{
    protected $table = 'accounting_project_customers';

    protected $fillable = ['team_id', 'customer_ref', 'name', 'status', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function projects(): HasMany
    {
        return $this->hasMany(ProjectJob::class, 'customer_id');
    }
}

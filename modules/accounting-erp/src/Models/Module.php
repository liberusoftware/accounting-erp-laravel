<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, string>|null $dependencies
 * @property array<string, mixed>|null $config
 */
class Module extends Model
{
    protected $fillable = [
        'name',
        'version',
        'description',
        'enabled',
        'dependencies',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'dependencies' => 'array',
            'config' => 'array',
        ];
    }

    public static function findByName(string $name): ?static
    {
        return static::query()->where('name', $name)->first();
    }
}

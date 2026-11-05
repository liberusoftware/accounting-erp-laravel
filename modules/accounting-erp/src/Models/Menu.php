<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\Accounting\Erp\Database\Factories\MenuFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

#[UseFactory(MenuFactory::class)]
class Menu extends Model
{
    use HasFactory, SoftDeletes;
    use IsTenantModel;

    #[\Override]
    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'order',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}

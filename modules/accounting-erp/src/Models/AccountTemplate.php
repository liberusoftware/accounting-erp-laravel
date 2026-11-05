<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTemplate extends Model
{
    use HasFactory;

    #[\Override]
    protected $fillable = [
        'name',
        'industry_type',
        'structure',
    ];

    #[\Override]
    protected $casts = [
        'structure' => 'array',
    ];
}

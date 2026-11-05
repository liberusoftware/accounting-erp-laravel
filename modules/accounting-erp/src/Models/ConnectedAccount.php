<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Erp\Database\Factories\ConnectedAccountFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

#[UseFactory(ConnectedAccountFactory::class)]
class ConnectedAccount extends Model
{
    use HasFactory, IsTenantModel;

    #[\Override]
    protected $fillable = [
        'provider',
        'provider_id',
        'name',
        'nickname',
        'email',
        'avatar_path',
        'expires_at',
    ];

    #[\Override]
    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($connectedAccount): void {
            if (empty($connectedAccount->user_id) && auth()->check()) {
                $connectedAccount->user_id = auth()->id();
            }
        });
    }
}

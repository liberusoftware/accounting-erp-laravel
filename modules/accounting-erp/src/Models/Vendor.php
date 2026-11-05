<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Liberu\Accounting\Erp\Database\Factories\VendorFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

/**
 * @property string|null $password
 * @property string|null $qbo_id
 * @property string|null $sage_id
 * @property string|null $xero_id
 * @property int|null $team_id
 */
#[UseFactory(VendorFactory::class)]
class Vendor extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use IsTenantModel;
    use Notifiable;

    #[\Override]
    protected $primaryKey = 'vendor_id';

    /** Auth guard for the vendor portal. */
    protected string $guard = 'vendor';

    #[\Override]
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_id',
        'payment_terms',
        'status',
        'qbo_id',
        'sage_id',
        'xero_id',
        'team_id',
    ];

    #[\Override]
    protected $hidden = [
        'password',
        'remember_token',
    ];

    #[\Override]
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Vendors may reach only their own portal — never the staff panels.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'vendor';
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'vendor_id', 'vendor_id');
    }

    public function vendorCredits()
    {
        return $this->hasMany(VendorCredit::class, 'vendor_id', 'vendor_id');
    }
}

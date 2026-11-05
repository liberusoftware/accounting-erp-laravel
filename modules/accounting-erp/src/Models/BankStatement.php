<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Erp\Database\Factories\BankStatementFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

#[UseFactory(BankStatementFactory::class)]
class BankStatement extends Model
{
    use HasFactory;
    use IsTenantModel;

    #[\Override]
    protected $fillable = [
        'statement_date',
        'account_id',
        'total_credits',
        'total_debits',
        'ending_balance',
        'reconciled',
        'team_id',
    ];

    #[\Override]
    protected $casts = [
        'statement_date' => 'date',
        'total_credits' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'ending_balance' => 'decimal:2',
        'reconciled' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

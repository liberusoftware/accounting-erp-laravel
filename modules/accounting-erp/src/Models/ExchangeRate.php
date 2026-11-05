<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Liberu\Accounting\Erp\Database\Factories\ExchangeRateFactory;

/**
 * @property-read Currency|null $fromCurrency
 * @property-read Currency|null $toCurrency
 * @property Carbon $date
 */
#[UseFactory(ExchangeRateFactory::class)]
class ExchangeRate extends Model
{
    use HasFactory;

    #[\Override]
    protected $primaryKey = 'exchange_rate_id';

    #[\Override]
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'date',
    ];

    #[\Override]
    protected $casts = [
        'rate' => 'float',
        'date' => 'date',
    ];

    /**
     * @return BelongsTo<Currency, $this>
     */
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    /**
     * @return BelongsTo<Currency, $this>
     */
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}

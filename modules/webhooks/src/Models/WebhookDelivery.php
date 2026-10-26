<?php

declare(strict_types=1);

namespace Liberu\Foundation\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $endpoint_id
 * @property string $event_id
 * @property string $event
 * @property array<string, mixed> $payload
 * @property int $attempts
 * @property-read WebhookEndpoint|null $endpoint
 */
final class WebhookDelivery extends Model
{
    protected $table = 'webhook_deliveries';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'endpoint_id', 'event_id', 'event', 'payload', 'status', 'attempts', 'response_status', 'response_excerpt', 'next_attempt_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'next_attempt_at' => 'datetime'];
    }

    /** @return BelongsTo<WebhookEndpoint, $this> */
    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'endpoint_id');
    }
}

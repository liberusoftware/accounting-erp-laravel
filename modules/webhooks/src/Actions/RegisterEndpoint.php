<?php

declare(strict_types=1);

namespace Liberu\Foundation\Webhooks\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Liberu\Foundation\Webhooks\Models\WebhookEndpoint;
use Liberu\Foundation\Webhooks\Support\SigningSecretVault;

final readonly class RegisterEndpoint
{
    public function __construct(private SigningSecretVault $vault) {}

    /** @param list<string> $events */
    public function handle(string $ownerRef, string $url, array $events, ?string $secret = null): WebhookEndpoint
    {
        if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array(parse_url($url, PHP_URL_SCHEME), ['https', 'http'], true)) {
            throw new InvalidArgumentException('The webhook URL is invalid.');
        }
        $events = array_values(array_unique(array_filter(array_map('strval', $events))));
        if ($events === []) {
            throw new InvalidArgumentException('At least one webhook event is required.');
        }
        $secret ??= Str::random(64);

        return DB::transaction(fn (): WebhookEndpoint => WebhookEndpoint::query()->create(['owner_ref' => $ownerRef, 'url' => $url, 'signing_secret' => $this->vault->seal($secret), 'events' => $events, 'active' => true]));
    }
}

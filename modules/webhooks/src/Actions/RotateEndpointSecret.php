<?php

declare(strict_types=1);

namespace Liberu\Foundation\Webhooks\Actions;

use Illuminate\Support\Str;
use Liberu\Foundation\Webhooks\Models\WebhookEndpoint;
use Liberu\Foundation\Webhooks\Support\SigningSecretVault;

final readonly class RotateEndpointSecret
{
    public function __construct(private SigningSecretVault $vault) {}

    public function handle(WebhookEndpoint $endpoint): string
    {
        $secret = Str::random(64);
        $endpoint->forceFill(['signing_secret' => $this->vault->seal($secret), 'rotated_at' => now()])->save();

        return $secret;
    }
}

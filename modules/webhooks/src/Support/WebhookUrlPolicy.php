<?php

declare(strict_types=1);

namespace Liberu\Foundation\Webhooks\Support;

use InvalidArgumentException;
use RuntimeException;

class WebhookUrlPolicy
{
    /** @return array<string, mixed> */
    public function options(string $url): array
    {
        $parts = parse_url($url);
        if (! filter_var($url, FILTER_VALIDATE_URL) || ! is_array($parts)
            || ! in_array($parts['scheme'] ?? '', ['https', 'http'], true)
            || isset($parts['user']) || isset($parts['pass'])) {
            throw new InvalidArgumentException('The webhook URL is invalid.');
        }

        $host = trim($parts['host'], '[]');
        $literal = filter_var($host, FILTER_VALIDATE_IP) !== false;
        $addresses = $literal ? [$host] : $this->addresses($host);
        if ($addresses === []) {
            throw new InvalidArgumentException('The webhook host could not be resolved.');
        }
        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_GLOBAL_RANGE)
                || str_starts_with(strtolower($address), '::ffff:')) {
                throw new InvalidArgumentException('Webhook destinations must use public IP addresses.');
            }
        }
        if (! extension_loaded('curl')) {
            throw new RuntimeException('Secure webhook delivery requires the cURL extension.');
        }

        $port = $parts['port'] ?? ($parts['scheme'] === 'https' ? 443 : 80);
        $pinned = array_map(static fn (string $address): string => str_contains($address, ':') ? '['.$address.']' : $address, $addresses);

        return [
            'allow_redirects' => false,
            'proxy' => '',
            'curl' => $literal ? [] : [CURLOPT_RESOLVE => [$host.':'.$port.':'.implode(',', $pinned)]],
        ];
    }

    /** @return list<string> */
    protected function addresses(string $host): array
    {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        $addresses = [];
        foreach ($records ?: [] as $record) {
            if (isset($record['ip'])) {
                $addresses[] = $record['ip'];
            } elseif (isset($record['ipv6'])) {
                $addresses[] = $record['ipv6'];
            }
        }

        return array_values(array_unique($addresses));
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * '*' trusts the immediate upstream (the k8s ingress / load balancer) so
     * getClientIp() and the request scheme resolve from X-Forwarded-* instead
     * of collapsing every request to the ingress pod IP — without which
     * rate-limit-by-IP buckets are shared across all users. Safe only because
     * the app is always deployed behind a trusted proxy.
     * ponytail: pin to the ingress CIDR if the app is ever exposed directly.
     *
     * @var array<int, string>|string|null
     */
    #[\Override]
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    #[\Override]
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    #[\Override]
    protected $except = [
        //
    ];
}

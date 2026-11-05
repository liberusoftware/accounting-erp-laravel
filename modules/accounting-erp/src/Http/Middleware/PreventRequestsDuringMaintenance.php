<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    #[\Override]
    protected $except = [
        //
    ];
}

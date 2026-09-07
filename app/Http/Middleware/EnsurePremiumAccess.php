<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePremiumAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('premium.enabled', false)) {
            return $next($request);
        }

        $team = $request->user()?->currentTeam;
        if (! $team instanceof Team || $team->hasPremiumAccess()) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();
        if ($routeName === 'dashboard'
            || str_contains($routeName, 'dashboard')
            || str_contains($routeName, 'overview')
            || str_contains($routeName, 'payment')
            || str_contains($routeName, 'export')
            || str_starts_with($routeName, 'billing.premium')) {
            return $next($request);
        }

        return redirect()->route('billing.premium')->with('error', 'Premium access is paused until billing is restored. Your overview, payments, and exports remain available.');
    }
}

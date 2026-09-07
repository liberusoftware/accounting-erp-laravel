<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Redirect newly registered teams to the first-use accounting setup wizard. */
final class EnsureAccountingSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $team = $request->user()?->currentTeam;

        if ($team !== null
            && $team->accounting_setup_completed_at === null
            && ! $request->routeIs('filament.app.pages.account-setup')) {
            return redirect('/app/account-setup');
        }

        return $next($request);
    }
}

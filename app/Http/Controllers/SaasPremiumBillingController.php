<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\SaasPremiumBillingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class SaasPremiumBillingController extends Controller
{
    public function show(Request $request): View
    {
        abort_unless(config('premium.enabled', false), 404);

        return view('billing.premium', ['team' => $this->team($request)]);
    }

    public function checkout(Request $request, SaasPremiumBillingService $billing): RedirectResponse
    {
        $team = $this->team($request);
        $interval = (string) $request->validate(['interval' => ['required', 'in:month,year,monthly,yearly']])['interval'];
        $session = $billing->createCheckoutSession($team, $interval, route('billing.premium.success'), route('billing.premium'));

        return redirect()->away((string) $session['url']);
    }

    public function portal(Request $request, SaasPremiumBillingService $billing): RedirectResponse
    {
        $team = $this->team($request);
        $session = $billing->createPortalSession($team, route('billing.premium'));

        return redirect()->away((string) $session['url']);
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('billing.premium')->with('status', 'Your Premium workspace is being activated.');
    }

    public function webhook(Request $request, SaasPremiumBillingService $billing): Response
    {
        $billing->handleWebhook($request->getContent(), $request->header('Stripe-Signature'));

        return response()->noContent();
    }

    private function team(Request $request): Team
    {
        $team = Team::query()->find($request->user()?->current_team_id);
        abort_unless($team instanceof Team && $request->user()->belongsToTeam($team), 403);
        abort_unless(config('premium.enabled', false), 404);

        return $team;
    }
}

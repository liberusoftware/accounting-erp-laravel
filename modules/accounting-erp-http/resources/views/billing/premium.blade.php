@extends('layouts.app')

@section('title', 'Premium billing')

@section('content')
<div class="mx-auto max-w-4xl px-6 py-16">
    <div class="rounded-3xl bg-slate-950 p-8 text-white shadow-xl sm:p-12">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Accounting ERP Premium</p>
        <h1 class="mt-4 text-4xl font-bold tracking-tight">More control for every close.</h1>
        <p class="mt-4 max-w-2xl text-slate-300">Start a 14-day trial for this team. Choose monthly or yearly billing, manage payment details securely in Stripe, and keep your financial operations moving.</p>
        <div class="mt-10 grid gap-4 sm:grid-cols-2">
            <form method="POST" action="{{ route('billing.premium.checkout') }}" class="rounded-2xl bg-white/10 p-6">
                @csrf
                <input type="hidden" name="interval" value="month">
                <h2 class="text-xl font-semibold">Monthly</h2>
                <p class="mt-2 text-slate-300">£4.99 per month after your trial.</p>
                <button class="mt-6 min-h-11 w-full rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300" type="submit">Start monthly trial</button>
            </form>
            <form method="POST" action="{{ route('billing.premium.checkout') }}" class="rounded-2xl bg-cyan-400 p-6 text-slate-950">
                @csrf
                <input type="hidden" name="interval" value="year">
                <h2 class="text-xl font-semibold">Yearly</h2>
                <p class="mt-2 text-slate-800">£49.99 per year after your trial.</p>
                <button class="mt-6 min-h-11 w-full rounded-xl bg-slate-950 px-5 py-3 font-semibold text-white hover:bg-slate-800" type="submit">Start yearly trial</button>
            </form>
        </div>
        @if ($team?->stripe_customer_id)
            <form method="POST" action="{{ route('billing.premium.portal') }}" class="mt-6">
                @csrf
                <button type="submit" class="text-sm font-semibold text-cyan-300 underline underline-offset-4">Manage billing securely in Stripe</button>
            </form>
        @endif
    </div>
</div>
@endsection

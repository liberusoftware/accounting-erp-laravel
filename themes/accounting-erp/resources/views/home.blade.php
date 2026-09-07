@php($premium = (bool) config('premium.enabled', false))
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $premium ? 'Accounting clarity for ambitious teams. Start your 14-day Premium trial.' : 'A calm, capable accounting workspace for growing businesses.' }}">
    <title>{{ $premium ? 'Accounting ERP Premium' : 'Accounting ERP' }}</title>
    @themeVite
</head>
<body class="erp-page">
<a class="skip-link" href="#main-content">{{ __('Skip to content') }}</a>
<header class="erp-nav">
    <div class="erp-nav__inner">
        <a class="erp-brand" href="{{ route('home') }}" aria-label="{{ config('app.name', 'Accounting ERP') }}">
            <span class="erp-brand__mark" aria-hidden="true">+</span><span>Accounting ERP</span>
        </a>
        <nav aria-label="{{ __('Primary navigation') }}" class="flex items-center gap-5">
            <a class="erp-nav__link erp-nav__link--desktop" href="#{{ $premium ? 'premium-workspace' : 'capabilities' }}">{{ $premium ? __('Why Premium') : __('Capabilities') }}</a>
            @auth <a class="erp-button erp-button--quiet" href="{{ route('dashboard') }}">{{ __('Open workspace') }}</a>
            @else <a class="erp-nav__link" href="{{ route('login') }}">{{ __('Sign in') }}</a> @endauth
        </nav>
    </div>
</header>
<main id="main-content">
    <section class="erp-hero">
        <div class="erp-wrap erp-hero__grid">
            <div>
                <p class="erp-kicker">{{ $premium ? __('A sharper way to run finance') : __('A better place for your books') }}</p>
                <h1 class="erp-title">{{ $premium ? __('Close with confidence. Grow with clarity.') : __('Accounting that keeps your business moving.') }}</h1>
                <p class="erp-lede">{{ $premium ? __('Bring your team, controls, and financial story into one calm command centre. Premium turns complexity into momentum.') : __('A focused workspace for invoices, expenses, reconciliations, and reports — with the essentials ready when you are.') }}</p>
                <div class="erp-actions">
                    @auth
                        @if ($premium)<a class="erp-button erp-button--accent" href="{{ route('billing.premium') }}">{{ __('Start your 14-day trial') }}</a>@endif
                        <a class="erp-button {{ $premium ? 'erp-button--quiet' : 'erp-button--primary' }}" href="{{ route('dashboard') }}">{{ __('Open your workspace') }}</a>
                    @else
                        <a class="erp-button erp-button--accent" href="{{ route('register') }}">{{ $premium ? __('Start your 14-day trial') : __('Create your workspace') }}</a>
                        <a class="erp-button erp-button--quiet" href="{{ route('login') }}">{{ __('Sign in') }}</a>
                    @endauth
                </div>
                <div class="erp-proof"><span>{{ $premium ? __('Built for ambitious teams') : __('Ready to use') }}</span><span>{{ __('Secure by design') }}</span><span>{{ $premium ? __('14 days to prove the difference') : __('Made for clarity') }}</span></div>
            </div>
            <div class="erp-dashboard" aria-label="{{ __('Illustration of an accounting performance dashboard') }}" role="img">
                <div class="erp-dashboard__top"><span>ACCOUNTING ERP</span><span>{{ $premium ? 'PREMIUM' : 'WORKSPACE' }}</span></div>
                <div class="erp-dashboard__body"><p class="text-sm text-slate-300">{{ $premium ? __('Financial pulse') : __('Business pulse') }}</p><div class="erp-dashboard__metric">£248,620</div><div class="erp-bars" aria-hidden="true"><i style="height:35%"></i><i style="height:50%"></i><i style="height:42%"></i><i style="height:65%"></i><i style="height:58%"></i><i style="height:78%"></i><i style="height:70%"></i><i style="height:92%"></i></div><div class="mt-5 flex justify-between text-xs text-slate-300"><span>{{ __('On track') }}</span><span class="text-cyan-200">+18.4%</span></div></div>
            </div>
        </div>
    </section>
    <section id="{{ $premium ? 'premium-workspace' : 'capabilities' }}" class="erp-section erp-section--white">
        <div class="erp-wrap">
            <div class="erp-section__heading"><p class="erp-kicker">{{ $premium ? __('The Premium advantage') : __('The essentials, thoughtfully arranged') }}</p><h2>{{ $premium ? __('Your numbers deserve a system that thinks ahead.') : __('Less hunting. More knowing.') }}</h2><p>{{ $premium ? __('Designed for teams who have outgrown scattered tools and want an operating rhythm that compounds.') : __('Keep the day-to-day work clear, connected, and easy to pick up.') }}</p></div>
            <div class="erp-cards">
                @if ($premium)
                    <article class="erp-card"><div class="erp-card__icon">↗</div><h3>{{ __('See around corners') }}</h3><p>{{ __('Turn live financial signals into confident decisions before the next close.') }}</p></article>
                    <article class="erp-card"><div class="erp-card__icon">◎</div><h3>{{ __('One team rhythm') }}</h3><p>{{ __('Give every collaborator the context, controls, and visibility to move together.') }}</p></article>
                    <article class="erp-card"><div class="erp-card__icon">✦</div><h3>{{ __('Scale without drag') }}</h3><p>{{ __('Build repeatable workflows that stay composed as your entities and volume grow.') }}</p></article>
                @else
                    <article class="erp-card"><div class="erp-card__icon">↗</div><h3>{{ __('A clear daily view') }}</h3><p>{{ __('See the records and tasks that matter without wading through noise.') }}</p></article>
                    <article class="erp-card"><div class="erp-card__icon">◎</div><h3>{{ __('A steady workflow') }}</h3><p>{{ __('Keep your core accounting work organised from first entry to final report.') }}</p></article>
                    <article class="erp-card"><div class="erp-card__icon">✦</div><h3>{{ __('Room to grow') }}</h3><p>{{ __('Start with a solid foundation that can meet your business where it goes next.') }}</p></article>
                @endif
            </div>
        </div>
    </section>
    @if ($premium)
    <section class="erp-section"><div class="erp-wrap"><div class="erp-cta"><p class="erp-kicker text-cyan-300">{{ __('Make the next close your best one') }}</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-5xl">{{ __('Try Premium for 14 days. Feel the difference in week one.') }}</h2><p class="mt-5 max-w-2xl text-slate-300">{{ __('Choose the rhythm that fits your team: £4.99 monthly or £49.99 yearly after your trial.') }}</p><div class="erp-actions">@auth<a class="erp-button erp-button--accent" href="{{ route('billing.premium') }}">{{ __('Choose your plan') }}</a>@else<a class="erp-button erp-button--accent" href="{{ route('register') }}">{{ __('Start your trial') }}</a>@endauth</div></div></div></section>
    @endif
</main>
<footer class="erp-footer"><div class="erp-wrap flex flex-wrap justify-between gap-3"><span>© {{ date('Y') }} {{ config('app.name', 'Accounting ERP') }}</span><span>{{ $premium ? __('A more confident financial future starts here.') : __('A calm foundation for better business.') }}</span></div></footer>
</body>
</html>

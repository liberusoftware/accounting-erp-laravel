<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Liberu\Accounting\Erp\Settings\GeneralSettings;
use Override;

class SocialLinksWidget extends Widget
{
    #[Override]
    public function render(): View
    {
        $settings = app(GeneralSettings::class);

        $links = [];

        if ($settings->github_url) {
            $links['GitHub'] = $settings->github_url;
        }

        if ($settings->facebook_url) {
            $links['Facebook'] = $settings->facebook_url;
        }

        if ($settings->twitter_url) {
            $links['Twitter'] = $settings->twitter_url;
        }

        if ($settings->youtube_url) {
            $links['YouTube'] = $settings->youtube_url;
        }

        return app(Factory::class)->make('accounting-erp-filament::filament.app.widgets.social-links-widget', [
            'links' => $links,
        ]);
    }
}

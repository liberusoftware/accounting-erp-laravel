<?php

use App\Filament\App\Widgets\SocialLinksWidget;
use App\Settings\GeneralSettings;
use Livewire\Livewire;

it('renders configured social links and rejects executable URLs', function (): void {
    $settings = Mockery::mock(GeneralSettings::class)->makePartial();
    $settings->facebook_url = 'https://facebook.com/example';
    $settings->twitter_url = 'https://twitter.com/example';
    $settings->github_url = 'https://github.com/example';
    $settings->youtube_url = 'javascript:alert(1)';
    app()->instance(GeneralSettings::class, $settings);

    Livewire::test(SocialLinksWidget::class)
        ->assertSeeHtml('href="https://github.com/example"')
        ->assertSeeHtml('rel="noopener noreferrer"')
        ->assertDontSeeHtml('javascript:alert(1)');
});

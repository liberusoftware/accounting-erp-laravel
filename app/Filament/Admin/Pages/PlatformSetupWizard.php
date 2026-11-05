<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class PlatformSetupWizard extends ManageGeneralSettings
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Platform setup';

    protected static ?string $title = 'Set up your accounting platform';

    protected static ?string $slug = 'platform-setup';

    protected static ?int $navigationSort = -10;

    public function getSubheading(): ?string
    {
        return 'Configure the platform identity and public contact details. Each business chooses its own accounting preferences in the app panel.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Wizard::make([
                Step::make('Platform identity')->description('How customers contact your platform.')->icon('heroicon-o-building-office')->schema([
                    TextInput::make('site_name')->label('Platform name')->required()->maxLength(255),
                    TextInput::make('site_email')->label('Support email')->email()->required()->maxLength(255),
                    TextInput::make('site_phone')->label('Support phone')->tel()->maxLength(255),
                    TextInput::make('site_address')->label('Business address')->maxLength(255),
                ])->columns(2),
                Step::make('Display preferences')->description('Set the defaults for your public site.')->icon('heroicon-o-globe-alt')->schema([
                    TextInput::make('site_country')->label('Platform country')->required()->maxLength(255),
                    TextInput::make('site_currency')->label('Public currency symbol')->required()->maxLength(10)
                        ->helperText('For public site display. Workspace currencies are configured separately.'),
                    TextInput::make('site_default_language')->label('Default language')->required()->maxLength(10)->helperText('Language code supported by this installation, for example en.'),
                    Textarea::make('footer_copyright')->label('Footer copyright')->required()->maxLength(500)->rows(2),
                ])->columns(2),
                Step::make('Review & save')->description('Check your details before applying them.')->icon('heroicon-o-check-badge')->schema([
                    Text::make(fn (Get $get): string => 'Platform: '.($get('site_name') ?? '')),
                    Text::make(fn (Get $get): string => 'Support: '.($get('site_email') ?? '')),
                    Text::make(fn (Get $get): string => 'Country: '.($get('site_country') ?? '').' · Currency symbol: '.($get('site_currency') ?? '')),
                    Text::make(fn (Get $get): string => 'Language: '.($get('site_default_language') ?? '')),
                    Text::make(fn (Get $get): string => 'Phone: '.($get('site_phone') ?: 'Not provided')),
                    Text::make(fn (Get $get): string => 'Address: '.($get('site_address') ?: 'Not provided')),
                    Text::make(fn (Get $get): string => 'Copyright: '.($get('footer_copyright') ?? '')),
                    Text::make('Save to apply these settings. Social links and other optional details remain available in General Settings.'),
                ]),
            ])->persistStepInQueryString('platform-step')->submitAction(view('filament.admin.pages.setup-submit')),
        ]);
    }

    public function getFormActions(): array
    {
        return [];
    }

    public function getSavedNotificationTitle(): ?string
    {
        return 'Platform settings saved';
    }
}

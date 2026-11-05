<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\User;

class PersonalAccessTokensPage extends Page
{
    #[\Override]
    protected string $view = 'accounting-erp-filament::filament.pages.profile.personal-access-tokens';

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Account';

    #[\Override]
    protected static ?int $navigationSort = 3;

    #[\Override]
    protected static ?string $title = 'Personal Access Tokens';

    public User $user;

    public function mount(): void
    {
        $this->user = Auth::user();
    }

    public function createApiToken(string $name): void
    {
        $this->user->createToken($name);
    }

    public function deleteApiToken(string $name): void
    {
        $this->user->tokens()->where('name', $name)->first()->delete();
    }

    #[\Override]
    public function getHeading(): string
    {
        return static::$title;
    }

    #[\Override]
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}

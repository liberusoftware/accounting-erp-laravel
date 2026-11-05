<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Pages\CreateUser;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Pages\EditUser;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Pages\ListUsers;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Pages\ViewUser;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Schemas\UserForm;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Users\Tables\UsersTable;
use UnitEnum;

class UserResource extends Resource
{
    #[\Override]
    protected static ?string $model = User::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    #[\Override]
    protected static ?string $navigationLabel = 'Users';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    protected static ?int $navigationSort = 1;

    #[\Override]
    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    // Defence-in-depth: the admin panel gate (User::canAccessPanel) already
    // blocks non-admins, but guard the Users resource itself so role edits
    // stay admin-only even if the panel gate ever regresses.
    public static function canAccess(): bool
    {
        return filament()->auth()->user()?->hasRole(['super_admin', 'admin']) ?? false;
    }
}

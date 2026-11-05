<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Erp\Models\Menu;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource\Pages\CreateMenu;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource\Pages\EditMenu;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource\Pages\ListMenus;

class MenuResource extends Resource
{
    #[\Override]
    protected static ?string $model = Menu::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('url')->url()->required()->maxLength(255),
            Select::make('parent_id')
                ->relationship('parent', 'name')
                ->searchable()
                ->preload(),
            TextInput::make('order')->numeric()->default(0)->required(),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('url'),
                TextColumn::make('parent.name')->label('Parent'),
                TextColumn::make('order')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}

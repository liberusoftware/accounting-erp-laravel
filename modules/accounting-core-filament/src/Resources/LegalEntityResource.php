<?php

namespace Liberu\Accounting\CoreFilament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\CoreFilament\Resources\LegalEntityResource\Pages\CreateLegalEntity;
use Liberu\Accounting\CoreFilament\Resources\LegalEntityResource\Pages\EditLegalEntity;
use Liberu\Accounting\CoreFilament\Resources\LegalEntityResource\Pages\ListLegalEntities;

final class LegalEntityResource extends Resource
{
    protected static ?string $model = LegalEntity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('registration_number')->maxLength(255),
            TextInput::make('currency_code')
                ->required()
                ->length(3)
                ->dehydrateStateUsing(fn (string $state): string => strtoupper($state)),
            Select::make('accounting_basis')->options(['accrual' => 'Accrual', 'cash' => 'Cash'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('currency_code')->badge(),
            TextColumn::make('accounting_basis')->badge(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => ListLegalEntities::route('/'),
            'create' => CreateLegalEntity::route('/create'),
            'edit' => EditLegalEntity::route('/{record}/edit'),
        ];
    }
}

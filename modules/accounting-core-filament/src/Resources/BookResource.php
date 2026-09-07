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
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\CoreFilament\Resources\BookResource\Pages\ListBooks;

final class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('legal_entity_id')->relationship('legalEntity', 'name')->required(), TextInput::make('name')->required()->maxLength(255), TextInput::make('code')->required()->maxLength(50), Select::make('accounting_basis')->options(['accrual' => 'Accrual', 'cash' => 'Cash'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('code')->badge(), TextColumn::make('legalEntity.name')->label('Legal entity'), TextColumn::make('accounting_basis')->badge()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListBooks::route('/')];
    }
}

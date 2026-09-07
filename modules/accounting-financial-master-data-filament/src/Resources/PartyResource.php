<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource\Pages\CreateParty;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource\Pages\EditParty;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource\Pages\ListParties;

final class PartyResource extends Resource
{
    protected static ?string $model = Party::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('legal_entity_id')->required()->numeric(), Select::make('type')->required()->options(['customer' => 'Customer', 'supplier' => 'Supplier']), TextInput::make('reference')->maxLength(64), TextInput::make('name')->required()->maxLength(255), TextInput::make('email')->email(), TextInput::make('phone')->maxLength(64), TextInput::make('tax_identifier')->maxLength(128), TextInput::make('credit_limit')->numeric()->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('type')->badge(), TextColumn::make('email')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()->sortable()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListParties::route('/'), 'create' => CreateParty::route('/create'), 'edit' => EditParty::route('/{record}/edit')];
    }
}

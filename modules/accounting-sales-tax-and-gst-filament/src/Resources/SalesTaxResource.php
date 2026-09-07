<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGstFilament\Resources\SalesTaxResource\Pages\ListSalesTax;

final class SalesTaxResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $model = SalesTaxRecord::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('context_id')->required(), TextInput::make('type')->required(), TextInput::make('jurisdiction')->required(), TextInput::make('rate')->numeric(), TextInput::make('taxable_base')->numeric(), TextInput::make('period_start')->type('date')->required(), TextInput::make('period_end')->type('date')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('context_id')->searchable(), TextColumn::make('type')->badge(), TextColumn::make('jurisdiction'), TextColumn::make('rate'), TextColumn::make('taxable_base'), TextColumn::make('liability'), TextColumn::make('status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListSalesTax::route('/')];
    }
}

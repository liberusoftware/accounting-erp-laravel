<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Leases\Models\Lease;

final class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static ?string $navigationLabel = 'Leases';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('lease_ref')->required(), TextInput::make('name')->required(), TextInput::make('lessor_ref')->required(), DatePicker::make('commencement_date')->required(), DatePicker::make('end_date')->required(), TextInput::make('currency')->required()->length(3), TextInput::make('payment_amount')->numeric()->required(), TextInput::make('useful_life_months')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('lease_ref')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('end_date')->date()->sortable(), TextColumn::make('currency'), TextColumn::make('status')->badge(), TextColumn::make('lease_liability'), TextColumn::make('right_of_use_asset')])->defaultSort('end_date', 'asc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListLeases::route('/')];
    }
}

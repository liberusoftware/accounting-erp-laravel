<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;
use Liberu\Accounting\SalesInvoicingFilament\Resources\SalesInvoiceResource\Pages\ListSalesInvoices;

final class SalesInvoiceResource extends Resource
{
    protected static ?string $model = SalesInvoice::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('invoice_number')->required(), DatePicker::make('invoice_date')->required(), DatePicker::make('due_on'), TextInput::make('currency')->required()->default('USD')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('invoice_number'), TextColumn::make('invoice_date')->date(), TextColumn::make('status')->badge(), TextColumn::make('total'), TextColumn::make('delivery_status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListSalesInvoices::route('/')];
    }
}

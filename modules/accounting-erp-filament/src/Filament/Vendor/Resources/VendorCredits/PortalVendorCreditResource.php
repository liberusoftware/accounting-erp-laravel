<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\VendorCredits;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\VendorCredit;
use Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\VendorCredits\Pages\ListPortalVendorCredits;

/**
 * The vendor's own credit notes — read-only, scoped to the logged-in vendor.
 */
class PortalVendorCreditResource extends Resource
{
    #[\Override]
    protected static ?string $model = VendorCredit::class;

    protected static bool $isScopedToTenant = false;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-refund';

    #[\Override]
    protected static ?string $navigationLabel = 'My Credits';

    #[\Override]
    protected static ?string $modelLabel = 'credit';

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('vendor_id', Auth::guard('vendor')->id());
    }

    #[\Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    #[\Override]
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('credit_date')->date()->sortable(),
                TextColumn::make('total_amount')->money()->sortable(),
                TextColumn::make('amount_remaining')->money()->sortable(),
                TextColumn::make('status')->badge(),
            ])
            ->defaultSort('credit_date', 'desc')
            ->toolbarActions([]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListPortalVendorCredits::route('/'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\FixedAssets\Actions\AcquireAsset;
use Liberu\Accounting\FixedAssets\Actions\AddAssetComponent;
use Liberu\Accounting\FixedAssets\Actions\AddAssetDocument;
use Liberu\Accounting\FixedAssets\Actions\ArchiveAsset;
use Liberu\Accounting\FixedAssets\Actions\CapitalizeAsset;
use Liberu\Accounting\FixedAssets\Actions\CreateCategory;
use Liberu\Accounting\FixedAssets\Actions\CreateCustodian;
use Liberu\Accounting\FixedAssets\Actions\CreateLocation;
use Liberu\Accounting\FixedAssets\Actions\DisposeAsset;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Events\AssetAcquired;
use Liberu\Accounting\FixedAssets\Events\AssetArchived;
use Liberu\Accounting\FixedAssets\Events\AssetCapitalized;
use Liberu\Accounting\FixedAssets\Events\AssetComponentAdded;
use Liberu\Accounting\FixedAssets\Events\AssetDisposed;
use Liberu\Accounting\FixedAssets\Events\AssetDocumentAdded;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Tests\TestCase;

final class AccountingFixedAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_lifecycle_and_supporting_records_are_transactional(): void
    {
        Event::fake();
        $category = app(CreateCategory::class)->handle([
            'category_ref' => 'IT',
            'name' => 'Information technology',
            'asset_account_ref' => '1500',
            'depreciation_account_ref' => '1590',
            'useful_life_months' => 60,
            'depreciation_method' => 'straight_line',
        ]);
        $asset = app(AcquireAsset::class)->handle($category, [
            'asset_ref' => 'FA-'.Str::upper(Str::random(8)),
            'name' => 'Laptop',
            'acquired_on' => '2026-08-25',
            'cost' => 1200,
            'salvage_value' => 100,
            'currency' => 'gbp',
        ]);

        self::assertSame(AssetStatus::Acquired, $asset->status);
        app(CapitalizeAsset::class)->handle($asset, 'statutory');
        app(AddAssetComponent::class)->handle($asset, [
            'component_ref' => 'RAM',
            'name' => 'Memory upgrade',
            'cost' => 100,
            'useful_life_months' => 36,
        ]);
        app(AddAssetDocument::class)->handle($asset, [
            'document_ref' => 'INV-1',
            'kind' => 'invoice',
            'file_ref' => 'files/invoice-1.pdf',
            'attached_by' => 'tester',
        ]);
        app(DisposeAsset::class)->handle($asset->refresh(), 'Replaced');
        app(ArchiveAsset::class)->handle($asset->refresh());

        $asset->refresh();
        self::assertSame(AssetStatus::Archived, $asset->status);
        self::assertSame(1300.0, (float) $asset->cost);
        self::assertCount(1, $asset->components);
        self::assertCount(1, $asset->books);
        self::assertCount(1, $asset->documents);
        Event::assertDispatched(AssetAcquired::class);
        Event::assertDispatched(AssetCapitalized::class);
        Event::assertDispatched(AssetComponentAdded::class);
        Event::assertDispatched(AssetDocumentAdded::class);
        Event::assertDispatched(AssetDisposed::class);
        Event::assertDispatched(AssetArchived::class);
    }

    public function test_location_and_custodian_are_tenant_owned_records(): void
    {
        $category = app(CreateCategory::class)->handle([
            'team_id' => 42,
            'category_ref' => 'FURN',
            'name' => 'Furniture',
            'asset_account_ref' => '1510',
            'depreciation_account_ref' => '1591',
            'useful_life_months' => 120,
            'depreciation_method' => 'straight_line',
        ]);
        $asset = app(AcquireAsset::class)->handle($category, [
            'team_id' => 42,
            'asset_ref' => 'FA-42',
            'name' => 'Desk',
            'acquired_on' => '2026-08-25',
            'cost' => 500,
            'currency' => 'GBP',
        ]);
        $location = app(CreateLocation::class)->handle(['team_id' => 42, 'location_ref' => 'HQ', 'name' => 'Head office']);
        $custodian = app(CreateCustodian::class)->handle(['team_id' => 42, 'custodian_ref' => 'USR-1', 'name' => 'A user']);

        $asset->update(['location_id' => $location->id, 'custodian_id' => $custodian->id]);

        self::assertSame(42, $asset->refresh()->team_id);
        self::assertSame($location->id, $asset->location_id);
        self::assertSame($custodian->id, $asset->custodian_id);
    }

    public function test_invalid_transitions_and_duplicates_are_rejected(): void
    {
        $category = app(CreateCategory::class)->handle([
            'category_ref' => 'VEH',
            'name' => 'Vehicles',
            'asset_account_ref' => '1520',
            'depreciation_account_ref' => '1592',
            'useful_life_months' => 60,
            'depreciation_method' => 'straight_line',
        ]);
        $asset = app(AcquireAsset::class)->handle($category, [
            'asset_ref' => 'FA-DUP',
            'name' => 'Van',
            'acquired_on' => '2026-08-25',
            'cost' => 10000,
            'currency' => 'GBP',
        ]);

        $this->expectException(InvalidAsset::class);
        app(CapitalizeAsset::class)->handle($asset, 'statutory');
        app(CapitalizeAsset::class)->handle($asset, 'statutory');
    }

    public function test_api_is_authenticated_and_tenant_scoped(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'API team', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        Sanctum::actingAs($user, ['accounting.fixed-assets.read', 'accounting.fixed-assets.write']);

        $categoryResponse = $this->postJson('/api/v1/accounting/fixed-assets/categories', [
            'category_ref' => 'API',
            'name' => 'API assets',
            'asset_account_ref' => '1530',
            'depreciation_account_ref' => '1593',
            'useful_life_months' => 36,
            'depreciation_method' => 'straight_line',
        ])->assertCreated();
        $categoryId = $categoryResponse->json('data.id');

        $this->postJson('/api/v1/accounting/fixed-assets', [
            'category_id' => $categoryId,
            'asset_ref' => 'API-1',
            'name' => 'API laptop',
            'acquired_on' => '2026-08-25',
            'cost' => 800,
            'currency' => 'GBP',
        ])->assertCreated()->assertJsonPath('data.attributes.asset_ref', 'API-1');

        $this->getJson('/api/v1/accounting/fixed-assets')->assertOk()->assertJsonCount(1, 'data');

        $otherUser = User::factory()->create();
        $otherTeam = Team::forceCreate(['user_id' => $otherUser->id, 'name' => 'Other API team', 'personal_team' => false]);
        $otherUser->forceFill(['current_team_id' => $otherTeam->id])->save();
        Sanctum::actingAs($otherUser, ['accounting.fixed-assets.read']);

        $this->getJson('/api/v1/accounting/fixed-assets')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_api_write_operations_require_the_write_ability(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Read-only team', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        Sanctum::actingAs($user, ['accounting.fixed-assets.read']);

        $this->postJson('/api/v1/accounting/fixed-assets/categories', [
            'category_ref' => 'READ', 'name' => 'Read-only', 'asset_account_ref' => '1500',
            'depreciation_account_ref' => '1590', 'useful_life_months' => 12, 'depreciation_method' => 'straight_line',
        ])->assertForbidden();
    }
}

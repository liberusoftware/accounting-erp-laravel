<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\RegionalPacks\Actions\CreateRegionalPack;
use Liberu\Accounting\RegionalPacks\Actions\PublishRegionalPack;
use Liberu\Accounting\RegionalPacks\Actions\RunComplianceTests;
use Liberu\Accounting\RegionalPacks\Enums\RegionalPackStatus;
use Liberu\Accounting\RegionalPacks\Exceptions\InvalidRegionalPack;

uses(RefreshDatabase::class);
it('requires every regional artifact and runs compliance tests', function (): void {
    $pack = app(CreateRegionalPack::class)->handle(['country_code' => 'GB', 'locale' => 'en_GB', 'currency' => 'GBP']);
    $artifacts = [];
    foreach (['tax', 'report', 'document_format', 'filing_calendar', 'account_template', 'terminology', 'compliance_test'] as $type) {
        $artifacts[] = ['type' => $type, 'key' => $type.'-default', 'definition' => $type === 'compliance_test' ? ['expected' => '1', 'actual' => '1'] : ['enabled' => true]];
    }$published = app(PublishRegionalPack::class)->handle($pack, $artifacts);
    expect($published->status)->toBe(RegionalPackStatus::Active)->and($published->artifacts)->toHaveCount(7);
    expect(app(RunComplianceTests::class)->handle($published)->artifacts->where('status', 'passed'))->toHaveCount(1);
});
it('rejects invalid countries and incomplete publication', function (): void {
    expect(fn () => app(CreateRegionalPack::class)->handle(['country_code' => 'GBR', 'locale' => 'en_GB', 'currency' => 'GBP']))->toThrow(InvalidRegionalPack::class);
    $pack = app(CreateRegionalPack::class)->handle(['country_code' => 'US', 'locale' => 'en_US', 'currency' => 'USD']);
    expect(fn () => app(PublishRegionalPack::class)->handle($pack, [['type' => 'tax', 'key' => 'sales', 'definition' => []]]))->toThrow(InvalidRegionalPack::class);
});

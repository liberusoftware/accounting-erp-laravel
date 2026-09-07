<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Enums;

enum RegionalArtifactType: string
{
    case Tax = 'tax';
    case Report = 'report';
    case DocumentFormat = 'document_format';
    case FilingCalendar = 'filing_calendar';
    case AccountTemplate = 'account_template';
    case Terminology = 'terminology';
    case ComplianceTest = 'compliance_test';
}

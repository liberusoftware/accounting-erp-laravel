<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ManagementReporting\Enums\ReportStatus;
use Liberu\Accounting\ManagementReporting\Exceptions\InvalidReport;
use Liberu\Accounting\ManagementReporting\Models\ReportDelivery;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class DeliverReport
{
    public function handle(ReportPack $report, string $format, array $attributes = []): ReportDelivery
    {
        if ($report->status !== ReportStatus::Approved) {
            throw new InvalidReport('Only approved reports can be delivered.');
        }if (! in_array($format, ['pdf', 'spreadsheet'], true)) {
            throw new InvalidReport('Delivery format must be pdf or spreadsheet.');
        }

        return DB::transaction(function () use ($report, $format, $attributes): ReportDelivery {
            $delivery = ReportDelivery::updateOrCreate(['report_pack_id' => $report->id, 'format' => $format], ['file_ref' => $attributes['file_ref'] ?? null, 'status' => 'delivered', 'recipients' => $attributes['recipients'] ?? [], 'checksum' => $attributes['checksum'] ?? null, 'delivered_at' => now(), 'metadata' => $attributes['metadata'] ?? null]);
            $report->update(['status' => ReportStatus::Delivered, 'delivered_at' => now()]);

            return $delivery;
        });
    }
}

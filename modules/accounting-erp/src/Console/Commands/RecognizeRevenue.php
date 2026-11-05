<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\RevenueSchedule;
use Liberu\Accounting\Erp\Services\RevenueRecognitionService;

class RecognizeRevenue extends Command
{
    #[\Override]
    protected $signature = 'revenue:recognize';

    #[\Override]
    protected $description = 'Post due revenue-recognition entries for all active schedules';

    public function handle(RevenueRecognitionService $service): void
    {
        $total = 0;
        RevenueSchedule::where('status', 'active')->each(function (RevenueSchedule $schedule) use (&$total, $service): void {
            try {
                $total += $service->recognizeDue($schedule);
            } catch (\Throwable $e) {
                report($e);
                $this->error("Schedule #{$schedule->getKey()} skipped: {$e->getMessage()}");
            }
        });
        $this->info("Recognised {$total} revenue period(s).");
    }
}

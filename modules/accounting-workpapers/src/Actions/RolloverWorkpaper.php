<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Workpapers\Models\Workpaper;

final class RolloverWorkpaper
{
    public function handle(Workpaper $source, array $attributes = []): Workpaper
    {
        return DB::transaction(function () use ($source, $attributes): Workpaper {
            $target = Workpaper::create(array_merge($source->only(['team_id', 'title', 'reference', 'period_start', 'period_end', 'preparer_id', 'reviewer_id', 'metadata']), ['status' => 'draft'], $attributes));
            foreach ($source->references as $reference) {
                $target->references()->create($reference->only(['label', 'target_type', 'target_id', 'notes']));
            }
            DB::table('accounting_workpaper_rollovers')->insert(['source_workpaper_id' => $source->id, 'target_workpaper_id' => $target->id, 'status' => 'completed', 'created_at' => now(), 'updated_at' => now()]);

            return $target;
        });
    }
}

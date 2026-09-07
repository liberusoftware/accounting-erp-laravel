<?php

declare(strict_types=1);

namespace Liberu\Accounting\Copilot\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Copilot\Models\CopilotRequest;

final class CreateCopilotRequest
{
    public function handle(array $attributes): CopilotRequest
    {
        return DB::transaction(fn (): CopilotRequest => CopilotRequest::create([
            ...$attributes,
            'status' => 'awaiting_confirmation',
        ]));
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Copilot\Models;

use Illuminate\Database\Eloquent\Model;

final class CopilotRequest extends Model
{
    protected $table = 'accounting_copilot_requests';

    protected $fillable = ['team_id', 'actor_id', 'kind', 'prompt', 'result', 'status', 'confirmation_key', 'metadata'];

    protected $casts = ['result' => 'array', 'metadata' => 'array'];
}

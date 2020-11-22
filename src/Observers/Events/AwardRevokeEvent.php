<?php

namespace Yormy\ReferralSystem\Observers\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AwardRevokeEvent
{
    use Dispatchable;
    use SerializesModels;

    public int $actionId;

    public string $deleteReason;

    public function __construct(int $actionId, string $deleteReason = "")
    {
        $this->actionId = $actionId;
        $this->deleteReason = $deleteReason;
    }
}

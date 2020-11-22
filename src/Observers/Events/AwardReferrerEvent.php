<?php

namespace Yormy\ReferralSystem\Observers\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AwardReferrerEvent
{
    use Dispatchable;
    use SerializesModels;

    public int $actionId;

    public function __construct(int $actionId)
    {
        $this->actionId = $actionId;
    }
}

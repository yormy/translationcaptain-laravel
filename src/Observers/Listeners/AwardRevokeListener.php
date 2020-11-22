<?php

namespace Yormy\ReferralSystem\Observers\Listeners;

use Illuminate\Support\Facades\Auth;
use Yormy\ReferralSystem\Models\ReferralAward;
use Yormy\ReferralSystem\Observers\Events\AwardRevokeEvent;
use Yormy\ReferralSystem\Traits\CookieTrait;

class AwardRevokeListener
{
    use CookieTrait;

    public function handle(AwardRevokeEvent $event)
    {
        $user = Auth::user();
        if ($user) {
            $latestReward = ReferralAward::with('user')
                ->where('user_id', $user->id)
                ->where('action_id', $event->actionId)
                ->latest('created_at')
                ->first();

            if ($latestReward) {
                $latestReward->delete_reason = $event->deleteReason;
                $latestReward->save();
                $latestReward->delete();
            }
        }
    }
}

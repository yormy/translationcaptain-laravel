<?php

namespace Yormy\TranslationcaptainLaravel\Observers\Listeners;

use Illuminate\Support\Facades\Auth;
use Yormy\TranslationcaptainLaravel\Models\ReferralAward;
use Yormy\TranslationcaptainLaravel\Observers\Events\AwardRevokeEvent;
use Yormy\TranslationcaptainLaravel\Traits\CookieTrait;

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

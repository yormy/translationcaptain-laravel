<?php

namespace Yormy\ReferralSystem\Observers\Listeners;

use Illuminate\Support\Facades\Auth;
use Yormy\ReferralSystem\Models\ReferralAward;
use Yormy\ReferralSystem\Observers\Events\AwardReferrerEvent;
use Yormy\ReferralSystem\Services\AwardService;
use Yormy\ReferralSystem\Traits\CookieTrait;

class AwardReferrerListener
{
    use CookieTrait;

    protected AwardService $awardService;

    public function __construct()
    {
        $this->awardService = new AwardService();
    }

    public function handle(AwardReferrerEvent $event)
    {
        $user = Auth::user();

        if ($user) {
            $publicReferrerId = $this->awardService->getReferrer();

            if (! $publicReferrerId) {
                $latestReward = $this->awardService->getReferringUserFromLatestAward($user->id);
                //if ($latestReward) {
                $publicReferrerId = $latestReward->referrer_id;
                //}
            }

            if ($publicReferrerId) {
                $referringUser = $this->awardService->getReferringUser($publicReferrerId);

                if ($referringUser) {
                    ReferralAward::create([
                        'user_id' => $user->id,
                        'referrer_id' => $referringUser->id,
                        'action_id' => $event->actionId,
                    ]);
                }
            }
        }
    }
}

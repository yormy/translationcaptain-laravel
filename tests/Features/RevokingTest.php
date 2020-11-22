<?php

namespace Yormy\ReferralSystem\Tests\Features;

use Illuminate\Support\Facades\Auth;
use Yormy\ReferralSystem\Models\ReferralAction;
use Yormy\ReferralSystem\Models\ReferralAward;
use Yormy\ReferralSystem\Observers\Events\AwardReferrerEvent;

use Yormy\ReferralSystem\Observers\Events\AwardRevokeEvent;
use Yormy\ReferralSystem\Tests\TestCase;

class RevokingTest extends TestCase
{
    /** @test */
    public function award_silver_recorded()
    {
        Auth::login($this->userBob);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));

        $referralAwardsCount = ReferralAward::where('referrer_id', $this->referrerFelix->id)->count();
        $this->assertEquals($referralAwardsCount, 1);

        event(new AwardRevokeEvent(ReferralAction::UPGRADE_SILVER));
        $referralAwardsCount = ReferralAward::where('referrer_id', $this->referrerFelix->id)->count();
        $this->assertEquals($referralAwardsCount, 0);
    }
}

<?php

namespace Yormy\ReferralSystem\Tests\Features;

use Illuminate\Support\Facades\Auth;
use Yormy\ReferralSystem\Models\ReferralAction;
use Yormy\ReferralSystem\Models\ReferralAward;

use Yormy\ReferralSystem\Observers\Events\AwardReferrerEvent;
use Yormy\ReferralSystem\Tests\TestCase;

class DetailsTest extends TestCase
{
    /** @test */
    public function no_referrals_yet()
    {
        Auth::login($this->referrerFelix);

        $response = $this->get('/details');
        $this->assertStringNotContainsString("SILVER", $response->getContent());
    }

    /** @test */
    public function award_silver_recorded()
    {
        Auth::login($this->userBob);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));

        Auth::login($this->referrerFelix);

        $response = $this->get('/details');
        $this->assertStringContainsString("SILVER", $response->getContent());
    }

    /** @test */
    public function count_total_paid_unpaid()
    {
        Auth::login($this->userBob);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));

        Auth::login($this->userAdam);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_GOLD));

        Auth::login($this->referrerFelix);
        $response = $this->get('/details');

        $this->assertStringContainsString("<span id='total'>500</span>", $response->getContent());
        $this->assertStringContainsString("<span id='paid'>0</span>", $response->getContent());
        $this->assertStringContainsString("<span id='unpaid'>500</span>", $response->getContent());
    }

    /** @test */
    public function award_without_cookie_using_previous()
    {
        Auth::login($this->userBob);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));

        Auth::login($this->userBob);
        $this->get('details?via=');
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_GOLD));

        $referralAwardsCount = ReferralAward::where('referrer_id', $this->referrerFelix->id)->count();

        $this->assertEquals($referralAwardsCount, 2);
    }
}

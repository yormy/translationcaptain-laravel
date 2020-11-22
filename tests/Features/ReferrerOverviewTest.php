<?php

namespace Yormy\ReferralSystem\Tests\Features;

use Illuminate\Support\Facades\Auth;
use Yormy\ReferralSystem\Models\ReferralAction;

use Yormy\ReferralSystem\Observers\Events\AwardReferrerEvent;
use Yormy\ReferralSystem\Tests\TestCase;

class ReferrerOverviewTest extends TestCase
{
    /** @test */
    public function no_referrers_yet()
    {
        Auth::login($this->referrerFelix);

        $response = $this->get('/referrers');
        $response->assertOk();
    }

    /** @test */
    public function referrers_present_and_count()
    {
        Auth::login($this->userBob);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));

        Auth::login($this->userAdam);
        $this->get('details?via='. $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_GOLD));

        Auth::login($this->referrerFelix);

        $response = $this->get('/referrers');
        $this->assertStringContainsString("<span id='total'>500</span>", $response->getContent());
        $this->assertStringContainsString("<span id='paid'>0</span>", $response->getContent());
        $this->assertStringContainsString("<span id='unpaid'>500</span>", $response->getContent());

        $this->assertStringContainsString("felixreferrer", $response->getContent());
    }

    /** @test */
    public function referrers_details()
    {
        Auth::login($this->userBob);
        $this->get('details?via=' . $this->referrerFelix->id);
        event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));

        $response = $this->get('/referrers/' . $this->referrerFelix->id);
        $response->assertOk();
    }
}

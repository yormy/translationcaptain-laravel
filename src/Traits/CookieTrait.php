<?php

namespace Yormy\ReferralSystem\Traits;

use Illuminate\Support\Facades\Cookie;

trait CookieTrait
{
    public function getReferrerFromCookie()
    {
        $cookieName = config('referral-system.cookie.name');
        if (request()->hasCookie($cookieName)) {
            $publicReferrerId = request()->cookie($cookieName);

            return $publicReferrerId;
        }

        return null;
    }

    public function setCookie($referringUserId)
    {
        $cookieName = config('referral-system.cookie.name');
        $cookieLifetime = config('referral-system.cookie.lifetime');

        if ($referringUserId) {
            Cookie::queue($cookieName, $referringUserId, $cookieLifetime);
        }
    }
}

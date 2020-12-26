<?php

namespace Yormy\TranslationcaptainLaravel\Traits;

use Illuminate\Support\Facades\Cookie;

trait CookieTrait
{
    public function getReferrerFromCookie()
    {
        $cookieName = config('translationcaptain.cookie.name');
        if (request()->hasCookie($cookieName)) {
            $publicReferrerId = request()->cookie($cookieName);

            return $publicReferrerId;
        }

        return null;
    }

    public function setCookie($referringUserId)
    {
        $cookieName = config('translationcaptain.cookie.name');
        $cookieLifetime = config('translationcaptain.cookie.lifetime');

        if ($referringUserId) {
            Cookie::queue($cookieName, $referringUserId, $cookieLifetime);
        }
    }
}

<?php

namespace Yormy\ReferralSystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Yormy\ReferralSystem\Traits\CookieTrait;

class ReferrerMiddleware
{
    use CookieTrait;

    protected $queryParam;

    protected $referrerClass;

    public function __construct()
    {
        $this->referrerClass = config('referral-system.models.referrer.class');
        $this->queryParam = config('referral-system.query_parameter');
    }

    public function handle(Request $request, Closure $next)
    {
        $referringUserId = $this->getReferrerFromParameter($request);

        if (! $referringUserId) {
            $referringUserId = $this->getReferrerFromCookie();
        }

        $this->setCookie($referringUserId);

        // add the referrer also to the current request, otherwise the info becomes only available
        // the next time a request is made.
        $request->request->add([$this->queryParam => $referringUserId]);

        return $next($request);
    }

    private function getReferrerFromParameter(Request $request) : ?string
    {
        return $request->input($this->queryParam);
    }
}

<?php

namespace Yormy\ReferralSystem\Services;

use Illuminate\Support\Facades\DB;
use Yormy\ReferralSystem\Models\ReferralAward;
use Yormy\ReferralSystem\Traits\CookieTrait;

class AwardService
{
    use CookieTrait;

    public function getGlobalTotal()
    {
        $points = ReferralAward::select('points')
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select(DB::raw('sum(points) as points'))
            ->first();

        return $points->points;
    }

    public function getGlobalPaid()
    {
        $points = ReferralAward::select('points')
            ->whereNotNull('payment_id')
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select(DB::raw('sum(points) as points'))
            ->first();

        return $points->points;
    }

    public function getGlobalUnpaid()
    {
        $points = ReferralAward::select('points')
            ->whereNull('payment_id')
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select(DB::raw('sum(points) as points'))
            ->first();

        return $points->points;
    }

    public function getReferrerTotal(int $referrerId)
    {
        $points = ReferralAward::select('points')
            ->where('referrer_id', $referrerId)
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select(DB::raw('sum(points) as points'))
            ->first();

        return $points->points;
    }

    public function getReferrerPaid(int $referrerId)
    {
        $points = ReferralAward::select('points')
            ->where('referrer_id', $referrerId)
            ->whereNotNull('payment_id')
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select(DB::raw('sum(points) as points'))
            ->first();

        return $points->points;
    }

    public function getReferrerUnpaid(int $referrerId)
    {
        $points = ReferralAward::select('points')
            ->where('referrer_id', $referrerId)
            ->whereNull('payment_id')
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select(DB::raw('sum(points) as points'))
            ->first();

        return $points->points;
    }

    public function getReferrer()
    {
        $queryParam = config('referral-system.query_parameter');
        $referrerIdFromRequest = request()->input($queryParam);

        if ($referrerIdFromRequest) {
            return $referrerIdFromRequest;
        }

        return $this->getReferrerFromCookie();
    }

    public function getReferringUser(string $publicReferrerId)
    {
        $referrerClass = config('referral-system.models.referrer.class');
        $modelIdColumn = config('referral-system.models.referrer.public_id');

        /**
        * @psalm-suppress UndefinedClass
        */
        return (new $referrerClass)->where($modelIdColumn, $publicReferrerId)->first();
    }

    public function getReferringUserFromLatestAward(int $referrerUserId)
    {
        return ReferralAward::with('user')
            ->where('user_id', $referrerUserId)
            ->latest('created_at')
            ->first();
    }
}

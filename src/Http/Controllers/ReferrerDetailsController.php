<?php

namespace Yormy\ReferralSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Yormy\ReferralSystem\Http\Controllers\Resources\ReferrerAwardedActionCollection;
use Yormy\ReferralSystem\Models\ReferralAward;
use Yormy\ReferralSystem\Services\AwardService;

class ReferrerDetailsController extends Controller
{
    public function show()
    {
        $referringUser = Auth::user();

        return $this->showViewFor($referringUser);
    }

    public function showForUser(string $publicReferrerId)
    {
        $awardService = new AwardService();
        $referringUser = $awardService->getReferringUser($publicReferrerId);

        return $this->showViewFor($referringUser);
    }

    public function showViewFor($referringUser)
    {
        $awardedActions = $this->getAwardedActions($referringUser);
        $points = $this->getTotalPoints($referringUser);

        return view('referral-system::user.details', [
            'awardedActions' => json_encode($awardedActions),
            'points' => json_encode($points),
        ]);
    }

    private function getAwardedActions($referringUser)
    {
        $awardedAction = ReferralAward::with(['action','user'])
            ->where('referrer_id', $referringUser->id)
            ->get();

        return (new ReferrerAwardedActionCollection($awardedAction))->toArray(Request());
    }

    private function getTotalPoints($referringUser)
    {
        $awardService = new AwardService();
        $totalPoints = $awardService->getReferrerTotal($referringUser->id);
        $paidPoints = $awardService->getReferrerPaid($referringUser->id);
        $unpaidPoints = $awardService->getReferrerUnpaid($referringUser->id);

        return [
            "total" => $totalPoints ?? 0,
            "paid" => $paidPoints ?? 0,
            "unpaid" => $unpaidPoints ?? 0,
        ];
    }
}

<?php

namespace Yormy\ReferralSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yormy\ReferralSystem\Models\ReferralAward;
use Yormy\ReferralSystem\Services\AwardService;

class ReferrerOverviewController extends Controller
{
    public function index()
    {
        $referrerClass = config('referral-system.models.referrer.class');
        $table = (new $referrerClass)->getTable();

        $modelIdColumn = config('referral-system.models.referrer.public_id');
        $modelNameColumn = config('referral-system.models.referrer.name');

        $allReferrers = ReferralAward::select('referrer_id', $table.".". $modelIdColumn, $table. ".". $modelNameColumn)
            ->leftJoin($table, 'referrer_id', '=', $table.'.id')
            ->groupBy('referrer_id')
            ->get();

        $lastAward = ReferralAward::select('referrer_id', DB::raw('max(created_at) as created_at'))
            ->groupBy('referrer_id')
            ->get()
            ->pluck('created_at', 'referrer_id');

        $points = ReferralAward::groupBy('referrer_id')
            ->leftJoin('referral_actions', 'referral_actions.id', '=', 'action_id')
            ->select('referrer_id', DB::raw('sum(points) as points'));

        $totalPoints = clone $points;
        $totalPoints = $totalPoints
            ->get()
            ->pluck('points', 'referrer_id');

        $unpaidPoints = clone $points;
        $unpaidPoints = $unpaidPoints
            ->whereNull('payment_id')
            ->get()
            ->pluck('points', 'referrer_id');

        $paidPoints = clone $points;
        $paidPoints = $paidPoints
            ->whereNotNull('payment_id')
            ->get()
            ->pluck('points', 'referrer_id');

        $referrers = [];
        foreach ($allReferrers as $referrerModel) {
            $referrerId = $referrerModel->referrer_id;

            $referrer = [];
            $referrer['id'] = $referrerModel->{$modelIdColumn};

            $referrer['name'] = $referrerModel->{$modelNameColumn};

            $referrer['total'] = $totalPoints->get($referrerId, 0);
            $referrer['paid'] = $paidPoints->get($referrerId, 0);
            $referrer['unpaid'] = $unpaidPoints->get($referrerId, 0);
            $referrer['created_at'] = $lastAward->get($referrerId, 0)->format(config('referral-system.datetime_format'));

            $referrers[] = (object)$referrer;
        }

        $awardService = new AwardService();
        $totalPoints = $awardService->getGlobalTotal();
        $paidPoints = $awardService->getGlobalPaid();
        $unpaidPoints = $awardService->getGlobalUnpaid();

        $points = [
            "total" => $totalPoints ?? 0,
            "paid" => $paidPoints ?? 0,
            "unpaid" => $unpaidPoints ?? 0,
        ];

        return view('referral-system::admin.overview', [
            'referrers' => json_encode($referrers),
            'points' => json_encode($points),
        ]);
    }
}

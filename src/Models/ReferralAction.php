<?php

namespace Yormy\ReferralSystem\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralAction extends Model
{
    const SIGNUP = 1;
    const UPGRADE_BRONZE = 100;
    const UPGRADE_SILVER = 110;
    const UPGRADE_GOLD = 120;
    const CHARGE_BRONZE = 1000;
    const CHARGE_SILVER = 1100;
    const CHARGE_GOLD = 1200;

    protected $fillable = [
        'id',
        "name",
        "description",
        "points",
    ];

//    public function awards()
//    {
//        return $this->hasMany(ReferralAward::class, 'action_id', 'id');
//    }
}

<?php

namespace Yormy\ReferralSystem\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralDomain extends Model
{
    public function user()
    {
        // return $this->belongsTo(config('referral-system.models.referrer.class'));
    }
}

<?php

namespace Yormy\ReferralSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralAward extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        "referrer_id",
        "action_id",
    ];

    protected $referrerClass;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->referrerClass = config('referral-system.models.referrer.class');
    }

    public function user()
    {
        return $this->belongsTo($this->referrerClass);
    }

//    public function referrer()
//    {
//        return $this->belongsTo($this->referrerClass, 'referrer_id', 'id');
//    }

    public function action()
    {
        return $this->hasOne(ReferralAction::class, 'id', 'action_id');
    }
}

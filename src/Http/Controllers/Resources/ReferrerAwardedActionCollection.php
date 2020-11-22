<?php

namespace Yormy\ReferralSystem\Http\Controllers\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReferrerAwardedActionCollection extends ResourceCollection
{
    public $collects = ReferrerAwardedAction::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}

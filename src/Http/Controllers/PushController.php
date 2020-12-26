<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class PushController extends Controller
{
    public function push()
    {
        $push = new PushService(config('translationcaptain.locales'));

        $response = $push->pushToRemote();
        dd($response->body());
    }
}

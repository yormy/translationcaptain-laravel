<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class PushController extends Controller
{
    public function push()
    {
        $locales = ['nl','en'];
        $push = new PushService($locales);

//        dd($push->getAllKeys());
        $allKeys = $push->pushToRemote();
        dd($allKeys);
    }
}

<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;
use Yormy\TranslationcaptainLaravel\Services\PullService;
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

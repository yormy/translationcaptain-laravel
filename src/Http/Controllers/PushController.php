<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderBlade;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class PushController extends Controller
{
    public function push()
    {
        $locales = config('translationcaptain.locales');
        $push = new PushService($locales);

        $response = $push->pushToRemote();
        dd($response->body());
    }
}

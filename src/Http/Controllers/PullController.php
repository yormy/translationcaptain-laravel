<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;
use Yormy\TranslationcaptainLaravel\Services\PullService;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class PullController extends Controller
{
    public function pull()
    {
        $pull = new PullService();
        $allKeys = $pull->pullFromRemote();
    }
}

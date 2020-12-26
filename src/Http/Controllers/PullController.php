<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Yormy\TranslationcaptainLaravel\Services\PullService;

class PullController extends Controller
{
    public function pull()
    {
        $pull = new PullService();
        $allKeys = $pull->pullFromRemote();
    }
}

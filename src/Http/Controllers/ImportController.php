<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class ImportController extends Controller
{
    public function import()
    {
        $locales = ['nl','en'];
        $push = new PushService($locales);
        $allKeys = $push->getAllKeys();
        dd($allKeys);


        die();

        return view('translationcaptain-laravel::overview', [
            'overview' => json_encode($messages),
        ]);
    }



}

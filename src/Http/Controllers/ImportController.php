<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;
use Yormy\TranslationcaptainLaravel\Services\PullService;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class ImportController extends Controller
{
    public function import()
    {
        // $this->push();
        $this->pull();


        die();

        return view('translationcaptain-laravel::overview', [
            'overview' => json_encode($messages),
        ]);
    }

    public function pull()
    {
        $locales = ['nl','en'];
        $pull = new PullService();
        $allKeys = $pull->pullFromRemote();

        //$allKeys = json_decode($allKeys, true);



        // export:
        $bladeFilesGenerator = new GeneratorBlade($allKeys);
        $bladeFilesGenerator->export();

        $bladeFilesGenerator = new GeneratorVue($allKeys);
        $bladeFilesGenerator->export();

        dd($allKeys);

        return;

        dd($allKeys);
    }

    public function push()
    {
        $locales = ['nl','en'];
        $push = new PushService($locales);
        $allKeys = $push->pushToRemote();
        dd($allKeys);

//        // remote:
//        $allKeys = [
//            "dsfdsfsdfds" => "jjjj",
//        ];
        $url = 'https://backend.bedrock.local/api/v1/translationcaptain/labels/upload';
        $url = 'localhost/api/v1/translationcaptain/labels/upload';
        // $url = 'https://webhook.site/cd97eddc-7bf8-4c86-8cea-4209e69da91e';

        $data = [
            'translations' => base64_encode(json_encode($allKeys)),
        ];

        $response = Http::post($url, $data);

        dd($response->body());


        // go push ????
        dd($allKeys);
    }
}

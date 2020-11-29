<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Yormy\TranslationcaptainLaravel\Services\SearchSources;

class SearchSourcesController extends Controller
{
    public function search()
    {
        $importer = new SearchSources();


        $messages = $importer->getMessages();
        dd($messages);


//        $messages2 =[];
//
//        foreach ($messages as $language => $languageMessages)
//        {
//            $messages2[$language] = Arr::dot($languageMessages);
//        }
//
//        dd($messages);
        //dd($messages2);
        //// todo flatten
        return view('translationcaptain-laravel::overview', [
            'overview' => json_encode($messages),
        ]);
    }
}

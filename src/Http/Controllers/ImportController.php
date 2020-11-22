<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Yormy\TranslationcaptainLaravel\Http\Controllers\Resources\ReferrerAwardedActionCollection;
use Yormy\TranslationcaptainLaravel\Models\ReferralAward;
use Yormy\TranslationcaptainLaravel\Services\AwardService;
use Yormy\TranslationcaptainLaravel\Services\ImportLaravel;

class ImportController extends Controller
{
    public function import()
    {
        $importer = new ImportLaravel();
        $messages = $importer->getMessages();

        $messages2 =[];

        foreach ($messages as $language => $languageMessages)
        {
            $messages2[$language] = Arr::dot($languageMessages);
        }
//
//        dd($messages);
//dd($messages2);
//// todo flatten
        return view('translationcaptain-laravel::overview', [
            'overview' => json_encode($messages),
        ]);
    }


}

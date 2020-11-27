<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Yormy\TranslationcaptainLaravel\Exceptions\DuplicateKeyException;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderBlade;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderVue;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;
use Yormy\TranslationcaptainLaravel\Services\LabelsExport;
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
        $locales = ['nl'];
        $blade = new ReaderBlade($locales);
        $bladeLabels = $blade->getMessages();
       // dd($bladeLabels['nl']['validation']);

        $vue = new ReaderVue($locales);
        $importPath = base_path(). DIRECTORY_SEPARATOR. 'resources/js/components/lang';
        $vue->setImportPath($importPath);
        $vueLabels = $vue->getMessages();

        $allLabels = $this->mergeLabels($bladeLabels, $vueLabels);

        //dd($bladeLabels);
        dd($allLabels['nl']);
        dd('done');


//
//        dd($messages['nl']['validations']);

        $bladeFilesGenerator = new GeneratorBlade($messages);
        $locales = ['nl'];
        $bladeFilesGenerator->export($locales);
        dd('done');

        $vueFilesGenerator = new GeneratorVue($messages);
        $locales = ['nl'];
        //$vueFilesGenerator->zipCurrentFiles();
        $vueFilesGenerator->export($locales);


        dd('done');

        die();

        return view('translationcaptain-laravel::overview', [
            'overview' => json_encode($messages),
        ]);
    }

    private function mergeLabels(array $origin, array $toMerge) : array
    {
        $this->checkMerge($origin, $toMerge);

        return array_merge_recursive($origin, $toMerge);
    }


    public function checkMerge(array $labels, array $labelsToMerge)
    {
        $labelsDotted = Arr::dot($labels);
        $labelsToMergeDotted = Arr::dot($labelsToMerge);

        foreach($labelsDotted as $key => $translation) {
            if (array_key_exists($key, $labelsToMergeDotted)) {

                $labelTranslation = $this->removeBinding($translation);
                $labelTranslationToMerge = $this->removeBinding($labelsToMergeDotted[$key]);

                if ($labelTranslation !== $labelTranslationToMerge) {
                    throw new DuplicateKeyException($key, $labelTranslation, $labelTranslationToMerge);
                }
            }
        }
    }

    public function removeBinding(string $translation): string
    {
        $start = config('translationcaptain-laravel.databinding.start');
        $end = config('translationcaptain-laravel.databinding.end');
        $pattern ="$start(.*?)$end";

        return preg_replace("/". $pattern ."/", '', $translation);
    }


}

<?php

namespace Yormy\TranslationcaptainLaravel\Http\Controllers;

use Yormy\TranslationcaptainLaravel\Services\Generators\BladeFilesGenerator;
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
        $importer = new ImportLaravel();
        $messages = $importer->getMessages();

        $bladeFilesGenerator = new BladeFilesGenerator($messages);
        $locales = ['nl'];

  //      $languagePath = App()['path.lang'];
  //      dd($languagePath);
//        $bladeFilesGenerator->setExportPath($languagePath);
        $bladeFilesGenerator->export($locales);

        dd('done');







        //======================

        //$labelExport = new LabelsExport(LabelsExport::FOR_VUE, LabelsExport::AS_JSON, $messages);
        $labelExport = new LabelsExport(LabelsExport::FOR_BLADE, LabelsExport::AS_ARRAY, $messages);
        //$labelExport = new LabelsExport(LabelsExport::FOR_BLADE, LabelsExport::AS_ARRAY);

        $locales = ['nl'];
        $labelExport->export($locales);
        //$labelExport->exportlabelsFromDb($locales);

die();
//dd($messages2);
//// todo flatten
        return view('translationcaptain-laravel::overview', [
            'overview' => json_encode($messages),
        ]);
    }




}

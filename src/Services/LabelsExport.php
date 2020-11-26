<?php

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class LabelsExport
{
    const FOR_BLADE = 1;
    const FOR_VUE = 2;

    const AS_ARRAY = 3;
    const AS_JSON = 4;

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;

    protected $export = [];

    protected $messages = [];

    protected $exportForType;

    protected $exportFormat;

    protected $tab = "    ";

    protected $zipFilename;

    protected $description = "______GENERATED BY TRANSLATION EXPORT______";

    protected $exportRoot;

    protected $exportPath;

    protected $fileOpen;

    protected $fileClose;

    protected $quote;
    protected $keyToValue;
    protected $objectOpen;
    protected $objectClose;

    /**
     * LabelsExport constructor.
     */
    public function __construct(int $exportForType, int $exportFormat, $messages)
    {

        $this->messages = $messages;

        $this->app = App();
        $this->files = new Filesystem();

        $this->exportForType = $exportForType;
        $this->exportFormat = $exportFormat;
        $this->exportRoot = "temp" . DIRECTORY_SEPARATOR. "translation_export";

        if ($exportForType == self::FOR_BLADE) {
            $this->exportPath = $this->exportRoot. DIRECTORY_SEPARATOR. "blade";
            $this->zipFilename = "laravel";

            $open = "";
            $open .= "<?php ". PHP_EOL. PHP_EOL;
            $open .= "//". $this->description. PHP_EOL;
            $open .= "return [";

            $this->fileOpen = $open;
            $this->fileClose = PHP_EOL. "];". PHP_EOL;
        }

        if ($exportForType == self::FOR_VUE) {
            $this->exportPath = $this->exportRoot. DIRECTORY_SEPARATOR. "vue";
            $this->zipFilename = "vue";

            $open = "";
            $open .= "{ ". PHP_EOL;
            $open .= $this->tab. '"'. $this->description. '" : "",'. PHP_EOL;
            $this->fileOpen = $open;
            $this->fileClose = PHP_EOL. "}". PHP_EOL;
        }

        if ($exportFormat == self::AS_ARRAY) {
            $this->quote = "'";
            $this->keyToValue = " => ";
            $this->objectOpen = "[";
            $this->objectClose= "]";
        }

        if ($exportFormat == self::AS_JSON) {
            $this->quote = '"';
            $this->keyToValue = " : ";
            $this->objectOpen = "{";
            $this->objectClose= "}";
        }
    }

//    public function exportlabelsFromDb(array $locales)
//    {
//        $translatableLabelsExport = new TranslatableLabelsExport();
//
//        foreach ($locales as $locale) {
//            dd($translatableLabelsExport->export($locale));
//        }
//    }

    /**
     * @param array $locales
     * @return string
     */
    public function export(array $locales)
    {
        //$this->importAllFileTranslations();

        foreach ($locales as $locale) {
            $this->exportTranslationsForLocale($locale);

            $roots = $this->prepareForType($locale);

            if ($this->exportFormat === self::AS_ARRAY) {
                $this->generate($roots, "php");
            }

            if ($this->exportFormat === self::AS_JSON) {
                $this->generate($roots, "json");
            }
        }

        return;
//        $zipDownload = $this->zipping();
//        $this->cleanup();
//
//        return $zipDownload;
    }

    private function getDiskPath()
    {
        return Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    }

//    private function zipping()
//    {
//        $zip_file = $this->zipFilename. '.zip';
//        $zip = new \ZipArchive();
//        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
//
//        $path = $this->getDiskPath().  $this->exportPath;
//
//        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
//        foreach ($files as $name => $file) {
//            // We're skipping all subfolders
//            if (!$file->isDir()) {
//                $filePath     = $file->getRealPath();
//
//                // extracting filename with substr/strlen
//                $relativePath = '' . substr($filePath, strlen($path) + 1);
//
//                $zip->addFile($filePath, $relativePath);
//            }
//        }
//        $zip->close();
//        return response()->download($zip_file);
//    }

    private function cleanup()
    {
        Storage::deleteDirectory($this->exportRoot);
    }


    /**
     * @param $locale
     * @return array
     */
    private function prepareForType($locale)
    {
        if (self::FOR_BLADE == $this->exportForType) {
            return $this->prepareForBlade($locale);
        }

        if (self::FOR_VUE == $this->exportForType) {
            return $this->prepareForVue($locale);
        }
    }

    private function generate($roots, $fileExtension = "php")
    {
        $quote = $this->quote;
        $keyToValue  = $this->keyToValue;
        $objectOpen  = $this->objectOpen;
        $objectClose  = $this->objectClose;

        foreach ($roots as $rootItem => $files) {
            foreach ($files as $fileName => $translations) {

                $filename = $rootItem. DIRECTORY_SEPARATOR. $fileName;
                $filename .= ".". $fileExtension;

                $fileLines = "";
                foreach ($translations as $key => $value) {
                    if ($fileLines) {
                        $fileLines .= ",";
                    }

                    $fileLines .= PHP_EOL. $this->tab;
                    $fileLines .= $quote. $key. $quote;
                    $fileLines .= $keyToValue;

                    $result = "";
                    $level = 2;
                    if (is_array($value)) {
                        $this->arrayToText($value, $result, $level);

                        $fileLines .= $objectOpen. $result;
                        $fileLines .= PHP_EOL. $this->tab. $objectClose;
                    } else {
                        $fileLines .= $quote. $value. $quote;
                    }
                }

                $fileContents = "";
                $fileContents .= $this->fileOpen;
                $fileContents .= $fileLines;
                $fileContents .= $this->fileClose;
                dd($this->exportPath. DIRECTORY_SEPARATOR. $filename);
                Storage::put($this->exportPath. DIRECTORY_SEPARATOR. $filename, $fileContents);
            }
        }
    }


    public function arrayToText($treeArray, &$result, &$level = 2)
    {
        $quote = $this->quote;
        $keyToValue  = $this->keyToValue;
        $objectOpen  = $this->objectOpen;
        $objectClose  = $this->objectClose;

        foreach ($treeArray as $key => $value) {
            $tab = "    ";
            $tabLevels ="";
            for ($i=0; $i < $level; $i++) {
                $tabLevels .= $tab;
            }

            $result .= PHP_EOL;

            $result .= $tabLevels;
            $result .= $quote. $key. $quote;
            $result .= $keyToValue;

            if (is_array($value)) {
                $level++;
                if ($level > 1) {
                    $result .= $objectOpen ;
                }
                $this->arrayToText($value, $result, $level);
            } else {
                $result .= $quote. $value. $quote;
                if ($key !== array_key_last($treeArray)) {
                    $result .= ",";
                }
                continue;
            }

            $level--;
            $tabLevels ="";
            for ($i=0; $i < $level; $i++) {
                $tabLevels .= $tab;
            }
            $result .= PHP_EOL. $tabLevels. $objectClose;
            if ($key !== array_key_last($treeArray)) {
                $result .= ",";
            }
        }
    }


    /**
     * @param string $locale
     * @return array
     */
    private function prepareForBlade(string $locale)
    {
        $translations = array();

        $langRoot = $this->export[$locale];
        foreach ($langRoot as $langDir => $langFiles) {
            foreach ($langFiles as $langFile => $content) {
                if ("/" === $langDir) {
                    $translations[$locale][$langFile] = $content;
                } else {
                    $translations[$locale][$langDir. DIRECTORY_SEPARATOR. $langFile] = $content;
                }
            }
        }

//        $vendorRoot = $this->export['vendor'];
//        foreach ($vendorRoot as $vendorDir => $vendorFiles) {
//            foreach ($vendorFiles as $vendorFile => $content) {
//                //echo "$vendorDir => $vendorFile<BR>";
//                $translations['vendor'][$vendorDir.
//                    DIRECTORY_SEPARATOR. $vendorFile] = $content;
//            }
//        }

        return $translations;
    }

    /**
     * @param string $locale
     * @return array
     */
    private function prepareForVue(string $locale)
    {
        $vueFilenames = array();

        $langRoot = $this->export[$locale];
        foreach ($langRoot as $langDir => $langFiles) {
            foreach ($langFiles as $langFile => $content) {
                if ("/" === $langDir) {
                    $vueFilenames[$langFile] = $content;
                } else {
                    $vueFilenames[$langDir. DIRECTORY_SEPARATOR. $langFile] = $content;
                }
            }
        }

        $vendorRoot = $this->export['vendor'];
        foreach ($vendorRoot as $vendorDir => $vendorFiles) {
            foreach ($vendorFiles as $vendorFile => $content) {
                // remove locale from path
                $vendorDirParts = explode(DIRECTORY_SEPARATOR, $vendorDir);
                unset($vendorDirParts[1]);
                $vendorDir = implode(DIRECTORY_SEPARATOR, $vendorDirParts);
                //echo "$vendorDir => $vendorFile<BR>";
                $vueFilenames[$vendorDir. DIRECTORY_SEPARATOR. $vendorFile] = $content;
            }
        }

        $vueTranslations = array();
        $vueTranslations[$locale] = $vueFilenames;
        return $vueTranslations;
    }


    /**
     * @param string $locale
     */
    private function exportTranslationsForLocale(string $locale)
    {
        $groups = $this->messages[$locale];
        foreach ($groups as $groupName => $groupArray) {
            $pathElements = explode(DIRECTORY_SEPARATOR, $groupName);

            $filename = $pathElements[ count($pathElements) -1 ];   // the last element of the groupname is the filename
            unset($pathElements[count($pathElements) -1]);

            $fullPath = implode(DIRECTORY_SEPARATOR, $pathElements);

            foreach ($groupArray as $msgKey => $msgValue) {
                if (isset($pathElements[0]) && $pathElements[0] === 'vendor') {
                    // vendor path has locale inside the path, deal with this
                    // $vendorPath = $pathElements[0] .DIRECTORY_SEPARATOR. $pathElements[1];

                    $fullPathParts = explode(DIRECTORY_SEPARATOR, $fullPath);
                    unset($fullPathParts[0]);
                    $vendorpath = implode(DIRECTORY_SEPARATOR, $fullPathParts);

                    $this->export['vendor'][$vendorpath][$filename][$msgKey] = $this->processMessage($msgValue);
                } else {
                    if ('' == $fullPath) {
                        $fullPath = DIRECTORY_SEPARATOR;
                    }

                    $this->export[$locale][$fullPath][$filename][$msgKey] = $this->processMessage($msgValue);
                }
            }
        }
    }

    /**
     * @param bool $replace
     * @param null $base
     * @param bool $import_group
     * @return int|mixed
     */
    private function importAllFileTranslations($replace = false, $base = null, $import_group = false)
    {
        //https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
        $counter = 0;
        //allows for vendor lang files to be properly recorded through recursion.
        $vendor = true;
        if ($base == null) {
            $base = $this->app['path.lang'];
            $vendor = false;
        }

        foreach ($this->files->directories($base) as $langPath) {
            $locale = basename($langPath);

            //import langfiles for each vendor
            if ($locale == 'vendor') {
                foreach ($this->files->directories($langPath) as $vendor) {
                    $counter += $this->importAllFileTranslations($replace, $vendor);
                }

                continue;
            }
            $vendorName = $this->files->name($this->files->dirname($langPath));

            foreach ($this->files->allfiles($langPath) as $file) {
                $info = pathinfo($file);
                $group = $info['filename'];
                if ($import_group) {
                    if ($import_group !== $group) {
                        continue;
                    }
                }

//                if (in_array($group, $this->config['exclude_groups'])) {
//                    continue;
//                }

                $subLangPath = str_replace($langPath. DIRECTORY_SEPARATOR, '', $info['dirname']);
//                $subLangPath = str_replace(DIRECTORY_SEPARATOR, '/', $subLangPath);
//                $langPath = str_replace(DIRECTORY_SEPARATOR, '/', $langPath);

                if ($subLangPath != $langPath) {
                    $group = $subLangPath. DIRECTORY_SEPARATOR. $group;
                }

                if (! $vendor) {
                    $translations = \Lang::getLoader()->load($locale, $group);
                } else {
                    $translations = include $file;
                    $group = 'vendor'.
                        DIRECTORY_SEPARATOR. $vendorName.
                        DIRECTORY_SEPARATOR. $locale.
                        DIRECTORY_SEPARATOR. $group;//.'/'.$file->getFilenameWithoutExtension();
                }

                if ($translations && is_array($translations)) {
                    foreach (Arr::dot($translations) as $key => $value) {
                        $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
                        $counter += $importedTranslation ? 1 : 0;
                    }
                }
            }
        }

        foreach ($this->files->files($this->app['path.lang']) as $jsonTranslationFile) {
            if (strpos($jsonTranslationFile, '.json') === false) {
                continue;
            }
            $locale = basename($jsonTranslationFile, '.json');
            $group = self::JSON_GROUP;
            $translations =
                \Lang::getLoader()->load($locale, '*', '*'); // Retrieves JSON entries of the given locale only
            if ($translations && is_array($translations)) {
                foreach ($translations as $key => $value) {
                    $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
                    $counter += $importedTranslation ? 1 : 0;
                }
            }
        }

        return $counter;
    }

    /**
     * @param $key
     * @param $value
     * @param $locale
     * @param $group
     * @param bool $replace
     * @return bool
     */
    private function importTranslation($dottedKey, $value, $locale, $group, $replace = false)
    {
        $ar = [$dottedKey => $value];

        $treeKey = $this->arrayUndot($ar);

        $new = array();
        $new[$locale][$group] = $treeKey;

        $this->messages = array_merge_recursive($this->messages, $new);

        return true;
    }


    public function arrayUndot($dotNotationArray)
    {
        $array = [];
        foreach ($dotNotationArray as $key => $value) {
            Arr::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * @param String $message
     * @return string
     */
    private function processMessage($message)
    {
        if ($this->exportForType === self::FOR_VUE) {
            $jsonMessage = json_encode($message);
            $jsonMessage = $this->processDataBindingVue($jsonMessage);
            $message = json_decode($jsonMessage, true);
        }
        return $message;
    }

    /**
     * @param String $message
     * @return string
     */
    private function processDataBindingVue(String $message) : string
    {
        $endingChars = "\.|;|:| |@|\(|\)";
        $pattern = ":([a-zA-Z]+?)($endingChars)";

        preg_match_all("/$pattern/", $message, $matches);
        if ($matches) {
            //$exactFind = $matches[0];
            $innerFind = $matches[1];

            foreach ($innerFind as $value) {
                $laravelBinding = ":$value";
                $vueBinding = "{". $value. "}";
                $message = str_ireplace($laravelBinding, $vueBinding, $message);
            }
        }
        return $message;
    }
}

<?php

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class ImportLaravel
{

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fileSystem;

    protected array $messages = [];

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    protected array $allFiles=[];

    protected array $all;

    public function __construct()
    {
        $this->app = App();
        $this->fileSystem = new Filesystem();
    }

    public function getMessages()
    {
        $baseDir = $this->app['path.lang'];

        $baseDir .= "/vendor/";
        //dd($baseDir);
        //$this->importPlainFileTranslations($baseDir, $baseDir);
        $this->importVendorFileTranslations($baseDir, $baseDir);
dd($this->all);
        return $this->messages;
    }

    public function importVendorFileTranslations($root, $baseDir)
    {
        $this->addVendorFiles($root, $baseDir);
        foreach ($this->fileSystem->directories($baseDir) as $langPath) {
            $this->importVendorFileTranslations($root, $langPath);
        }
    }


    public function importPlainFileTranslations($root, $baseDir)
    {
        $this->addFiles($root, $baseDir);
        foreach ($this->fileSystem->directories($baseDir) as $langPath) {
            $this->importPlainFileTranslations($root, $langPath);
        }
    }

    public function addVendorFiles($root, $langPath)
    {
        foreach ($this->fileSystem->allfiles($langPath) as $file) {
            $fullPathname = $file->getPathname();
            $relative = str_replace($root, '', $fullPathname);

            // remove php extension
            $relative = substr($relative,0, strlen($relative) - strlen('.php'));

            // determine filename vs keyname
            $parts = explode(DIRECTORY_SEPARATOR, $relative);

            $group = $parts[0];
            $language = $parts[1];
            unset($parts[0]);
            unset($parts[1]);

            $keyprefix = implode('.', $parts);

            $translations = $this->convertImportfileToArray($fullPathname);

            if ($keyprefix) {
                $keyprefix .= ".";
            }

            $keysForGroup = [];
            foreach ($translations as $key => $translation)
            {

                $fullKey = $keyprefix. $key;

                $keysForGroup[$fullKey] = $translation;

             }

            $this->all[$language][$group] = $keysForGroup;
        }
    }


    public function addFiles($root, $langPath)
    {
        foreach ($this->fileSystem->allfiles($langPath) as $file) {

            $fullPathname = $file->getPathname();
            $relative = str_replace($root, '', $fullPathname);

            // remove php extension
            $relative = substr($relative,0, strlen($relative) - strlen('.php'));

            // determine filename vs keyname
            $parts = explode(DIRECTORY_SEPARATOR, $relative);

            $group = $parts[0];
            unset($parts[0]);
            $keyprefix = implode('.', $parts);

            $this->allFiles[] = $group . " / ". $keyprefix;



            $translations = $this->convertImportfileToArray($fullPathname);
            if ($keyprefix) {
                $keyprefix .= ".";
            }

            foreach ($translations as $key => $translation)
            {
                $this->messages[] = $group . " | ". $keyprefix . "$key => $translation";
            }
        }

    }

    public function convertImportfileToArray(string $filename)
    {
        $arrayTranslations = include $filename;
        $keyValues = Arr::dot($arrayTranslations);

        // Arr::dot convert an empty array not to a dotted value but remains an empty array.
        // Remove this empty array so we can trust on a single dimensional array
        foreach ($keyValues as $key => $value) {
            if (is_array($value)) {
               unset ($keyValues[$key]);
            }
        }
        return $keyValues;

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
}

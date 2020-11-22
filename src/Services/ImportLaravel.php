<?php declare(strict_types = 1);

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
        $importFromDir = $this->app['path.lang'];

        $languages =['en','nl','ar'];
        $languages =['en'];

       // $importFromDir .= "/vendor/";
        //$importFromDir .= "/en/";
        //dd($baseDir);
        foreach ($languages as $language) {
            $importFromLanguageDir = $importFromDir . DIRECTORY_SEPARATOR. $language;
            $this->importAppFileTranslations($language, $importFromLanguageDir, $importFromLanguageDir);
        }
        //$this->importVendorFileTranslations($importFromDir, $importFromDir);
dd($this->all);
        return $this->messages;
    }

    /**
     * Recursive walk the directory tree and import the files according to
     * the laravel package structure: lang/vendor/<vendorname>/<language>/group/group.php file stucture
     *
     * @param $root
     * @param $baseDir
     */
    public function importVendorFileTranslations(string $root, string $importFromDir):  void
    {
        $this->addVendorFiles($root, $importFromDir);
        foreach ($this->fileSystem->directories($importFromDir) as $currentDir) {
            $this->importVendorFileTranslations($root, $currentDir);
        }
    }

    /**
     * Recursive walk the directory tree and import the files according to
     * the laravel base structure: lang/<language>/group/group.php file stucture
     *
     * @param $root
     * @param $baseDir
     */
    public function importAppFileTranslations(string $language, string $root, string $importFromDir) : void
    {
        $this->addAppFiles($language, $root, $importFromDir);
        foreach ($this->fileSystem->directories($importFromDir) as $currentDir) {
            $this->importAppFileTranslations($language, $root, $currentDir);
        }
    }

    /**
     * Loop through the entire import directory and add all keys in all files to the array
     * @param string $root
     * @param string $importFromDir
     */
    public function addVendorFiles(string $root, string $importFromDir)
    {
        foreach ($this->fileSystem->allfiles($importFromDir) as $file) {
            $fullPathname = $file->getPathname();
            $relativePathname = str_replace($root, '', $fullPathname);

            // remove php extension
            $relativePathname = substr($relativePathname,0, strlen($relativePathname) - strlen('.php'));

            // strip leading directory separator
            $first = substr($relativePathname, 0,1);
            if ($first === DIRECTORY_SEPARATOR) {
                $relativePathname = substr($relativePathname,1, strlen($relativePathname));
            }

            // determine filename vs keyname
            $parts = explode(DIRECTORY_SEPARATOR, $relativePathname);

            $package = $parts[0];
            $language = $parts[1];
            unset($parts[0]);
            unset($parts[1]);

            // All keys of this group are prefixed with entire directory path
            $keyPrefix = implode('.', $parts);
            if ($keyPrefix) {
                $keyPrefix .= ".";
            }

            $keysForPackage = [];
            $translations = $this->convertImportfileToArray($fullPathname);
            foreach ($translations as $key => $translation)
            {
                $fullKey = $keyPrefix. $key;
                $keysForPackage[$fullKey] = $translation;
             }

            $this->all[$language][$package] = $keysForPackage;
        }
    }


    public function addAppFiles(string $language, string $root, string $langPath)
    {
        foreach ($this->fileSystem->allfiles($langPath) as $file) {

            $fullPathname = $file->getPathname();
            $relative = str_replace($root, '', $fullPathname);

            // remove php extension
            $relative = substr($relative,0, strlen($relative) - strlen('.php'));

            // strip leading directory separator
            $first = substr($relative, 0,1);
            if ($first === DIRECTORY_SEPARATOR) {
                $relative = substr($relative,1, strlen($relative));
            }

            // determine filename vs keyname
            $parts = explode(DIRECTORY_SEPARATOR, $relative);



            $group = $parts[0];
            unset($parts[0]);





            // All keys of this group are prefixed with entire directory path
            $keyPrefix = implode('.', $parts);
            if ($keyPrefix) {
                $keyPrefix .= ".";
            }

            $keysForPackage = [];
            $translations = $this->convertImportfileToArray($fullPathname);
            foreach ($translations as $key => $translation)
            {
                $fullKey = $keyPrefix. $key;
                $keysForPackage[$fullKey] = $translation;
            }

            $this->all[$language][$group] = $keysForPackage;


//            $translations = $this->convertImportfileToArray($fullPathname);
//            if ($keyPrefix) {
//                $keyPrefix .= ".";
//            }
//
//            foreach ($translations as $key => $translation)
//            {
//                $this->messages[] = $group . " | ". $keyPrefix . "$key => $translation";
//            }
        }

    }

    /**
     * Convert the php array structure text file into an php array object with dot notations
     *
     * @param string $filename
     * @return array
     */
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

//
//    /**
//     * @param bool $replace
//     * @param null $base
//     * @param bool $import_group
//     * @return int|mixed
//     */
//    private function importAllFileTranslations($replace = false, $base = null, $import_group = false)
//    {
//        //https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
//        $counter = 0;
//        //allows for vendor lang files to be properly recorded through recursion.
//        $vendor = true;
//        if ($base == null) {
//            $base = $this->app['path.lang'];
//            $vendor = false;
//        }
//
//        foreach ($this->files->directories($base) as $langPath) {
//            $locale = basename($langPath);
//
//            //import langfiles for each vendor
//            if ($locale == 'vendor') {
//                foreach ($this->files->directories($langPath) as $vendor) {
//                    $counter += $this->importAllFileTranslations($replace, $vendor);
//                }
//
//                continue;
//            }
//            $vendorName = $this->files->name($this->files->dirname($langPath));
//
//            foreach ($this->files->allfiles($langPath) as $file) {
//                $info = pathinfo($file);
//                $group = $info['filename'];
//                if ($import_group) {
//                    if ($import_group !== $group) {
//                        continue;
//                    }
//                }
//
////                if (in_array($group, $this->config['exclude_groups'])) {
////                    continue;
////                }
//
//                $subLangPath = str_replace($langPath. DIRECTORY_SEPARATOR, '', $info['dirname']);
////                $subLangPath = str_replace(DIRECTORY_SEPARATOR, '/', $subLangPath);
////                $langPath = str_replace(DIRECTORY_SEPARATOR, '/', $langPath);
//
//                if ($subLangPath != $langPath) {
//                    $group = $subLangPath. DIRECTORY_SEPARATOR. $group;
//                }
//
//                if (! $vendor) {
//                    $translations = \Lang::getLoader()->load($locale, $group);
//                } else {
//                    $translations = include $file;
//                    $group = 'vendor'.
//                        DIRECTORY_SEPARATOR. $vendorName.
//                        DIRECTORY_SEPARATOR. $locale.
//                        DIRECTORY_SEPARATOR. $group;//.'/'.$file->getFilenameWithoutExtension();
//                }
//
//                if ($translations && is_array($translations)) {
//                    foreach (Arr::dot($translations) as $key => $value) {
//                        $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
//                        $counter += $importedTranslation ? 1 : 0;
//                    }
//                }
//            }
//        }
//
//        foreach ($this->files->files($this->app['path.lang']) as $jsonTranslationFile) {
//            if (strpos($jsonTranslationFile, '.json') === false) {
//                continue;
//            }
//            $locale = basename($jsonTranslationFile, '.json');
//            $group = self::JSON_GROUP;
//            $translations =
//                \Lang::getLoader()->load($locale, '*', '*'); // Retrieves JSON entries of the given locale only
//            if ($translations && is_array($translations)) {
//                foreach ($translations as $key => $value) {
//                    $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
//                    $counter += $importedTranslation ? 1 : 0;
//                }
//            }
//        }
//
//        return $counter;
//    }
//
//    /**
//     * @param $key
//     * @param $value
//     * @param $locale
//     * @param $group
//     * @param bool $replace
//     * @return bool
//     */
//    private function importTranslation($dottedKey, $value, $locale, $group, $replace = false)
//    {
//        $ar = [$dottedKey => $value];
//
//        $treeKey = $this->arrayUndot($ar);
//
//        $new = array();
//        $new[$locale][$group] = $treeKey;
//
//        $this->messages = array_merge_recursive($this->messages, $new);
//
//        return true;
//    }
//
//
//    public function arrayUndot($dotNotationArray)
//    {
//        $array = [];
//        foreach ($dotNotationArray as $key => $value) {
//            Arr::set($array, $key, $value);
//        }
//
//        return $array;
//    }
}

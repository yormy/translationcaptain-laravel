<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class ImportLaravel
{

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fileSystem;

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    protected array $messages;

    protected array $languages;

    protected $defaultGroup = "basedefault";

    /**
     * Published vendor translations
     * lang/vendor/<package-name>/<language>/directory/directory/translations.php
     */
    const VENDOR_FILES = 0;

    /**
     * App translations
     * lang/<language>/directory/directory/translations.php
     */
    const APP_FILES = 1;

    /**
     * App translations
     * lang/en.php
     */
    const APP_SINGLE_FILES = 2;

    public function __construct()
    {
        $this->app = App();
        $this->fileSystem = new Filesystem();

        $this->languages =['en','nl','ar'];
    }

    public function getMessages()
    {
        $importFromDir = $this->app['path.lang'];

        foreach ($this->languages as $language) {
            $filename = $importFromDir. DIRECTORY_SEPARATOR. $language. ".php";
            $this->addSingleTranslationFiles(self::APP_SINGLE_FILES, $filename, $importFromDir, $language);
        }

        foreach ($this->languages as $language) {
            $importFromLanguageDir = $importFromDir . DIRECTORY_SEPARATOR. $language;
            $this->importFileTranslations(self::APP_FILES, $importFromLanguageDir, $importFromLanguageDir, $language);
        }

        $importFromVendorDir = $importFromDir . "/vendor";
        $this->importFileTranslations(self::VENDOR_FILES, $importFromVendorDir, $importFromVendorDir, $language);

        //d($this->messages);
        return $this->messages;
    }

    /**
     * Recursive walk the directory tree and import the files according to
     * the laravel base structure: lang/<language>/group/group.php file stucture
     *
     * @param $root
     * @param $baseDir
     */
    public function importFileTranslations(int $directoryType, string $root, string $importFromDir, string $language = null) : void
    {
        if (!is_dir($importFromDir)) {
            return;
        }

        $this->addTranslationFiles($directoryType, $root, $importFromDir, $language);
        foreach ($this->fileSystem->directories($importFromDir) as $currentDir) {
            $this->importFileTranslations($directoryType, $root, $currentDir, $language);
        }
    }

    public function addTranslationFiles(int $directoryType, string $root, string $importFromDir, string $language = null)
    {
        foreach ($this->fileSystem->files($importFromDir) as $file) {
            $fullPathname = $file->getPathname();
            $this->addSingleTranslationFiles($directoryType, $fullPathname, $root, $language);
        }
    }

    public function addSingleTranslationFiles(int $directoryType, string $fullPathname, string $root, string $language = null)
    {
        if (!is_file($fullPathname)) {
            return;
        }


        $relative = str_replace($root, '', $fullPathname);



        // remove php extension
        $relative = substr($relative,0, strlen($relative) - strlen('.php'));

        // strip leading directory separator
        $first = substr($relative, 0,1);
        if ($first === DIRECTORY_SEPARATOR) {
            $relative = substr($relative,1, strlen($relative));
        }
//        echo $relative. "<br>";
//        return;
        // determine filename vs keyname


        $groupPrefix ="###";

        if (self::VENDOR_FILES === $directoryType) {

            $lastDirSep = (int)strrpos($relative, DIRECTORY_SEPARATOR);

            $filename = substr($relative, $lastDirSep + 1, strlen($relative));
            $path = substr($relative, 0, $lastDirSep);

            $parts = explode(DIRECTORY_SEPARATOR, $path);
            $vendor = $parts[0];
            $language = $parts[1];
            unset($parts[0]);
            unset($parts[1]);

            $newPath = implode(DIRECTORY_SEPARATOR, $parts);
            if ($newPath) {
                $newPath.= DIRECTORY_SEPARATOR;
            }
            $relative = $vendor. "::". $newPath.  $filename;

        }
//        if (self::APP_FILES === $directoryType) {
//            $group = $parts[0];
//            unset($parts[0]);
//        }
//        if (self::APP_SINGLE_FILES === $directoryType) {
//            $group = $this->defaultGroup;
//        }

        // All keys of this group are prefixed with entire directory path
//        $keyPrefix = implode('.', $parts);
//        if ($keyPrefix) {
//            $keyPrefix .= ".";
//        }

        $keysForPackage = [];
        $translations = $this->convertImportfileToArray($fullPathname);
        foreach ($translations as $key => $translation)
        {
//            $fullKey = $keyPrefix. $key;
//
//            if (self::APP_SINGLE_FILES === $directoryType) {
//                $fullKey = $key;
//            }

            $keysForPackage[$key] = $translation;
        }

        $this->messages[$language][$relative] = $keysForPackage;
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

}

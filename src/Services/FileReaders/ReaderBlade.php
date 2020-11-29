<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileReaders;

use Illuminate\Support\Arr;

class ReaderBlade extends FileReader
{

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


    public function __construct(array $locales)
    {
        $this->importPath = App()['path.lang'];

        $endingChars = "\.|;|:| |$|@|\(|\)";
        $this->dataBindingPattern = ":([a-zA-Z]+?)($endingChars)";

        parent::__construct($locales);
    }

    public function getMessages()
    {
        foreach ($this->locales as $locale) {
            $filename = $this->importPath . DIRECTORY_SEPARATOR . $locale . ".php";
            $this->addSingleTranslationFiles(self::APP_SINGLE_FILES, $filename, $this->importPath, $locale);
        }

        foreach ($this->locales as $locale) {
            $importFromLanguageDir = $this->importPath . DIRECTORY_SEPARATOR . $locale;
            $this->importFileTranslations(self::APP_FILES, $importFromLanguageDir, $importFromLanguageDir, $locale);
        }

        $importFromVendorDir = $this->importPath . "/vendor";
        $this->importFileTranslations(self::VENDOR_FILES, $importFromVendorDir, $importFromVendorDir, $locale);

        return $this->messages;
    }

    public function addSingleTranslationFiles(int $directoryType, string $fullPathname, string $root, string $language = null) : void
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

        $keysForPackage = [];
        $translations = $this->convertImportfileToArray($fullPathname);
        foreach ($translations as $key => $translation) {
            $keysForPackage[$key] = $this->processTranslation($translation);
        }

        if (strcasecmp($relative, $language) === 0 ) {
            $relative= config('translationcaptain-laravel.group_when_group_missing');
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

        $keyValues = $this->fixEmptyArray($keyValues);
        return $keyValues;
    }

    protected function processTranslation(string $translation) : string
    {
        $translation = $this->createNewDataBinding($translation);
        return $translation;
    }

    protected function getRawDataBinding($value) : string
    {
        return ":$value";
    }
}

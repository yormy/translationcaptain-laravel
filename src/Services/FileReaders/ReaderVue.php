<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileReaders;

use Illuminate\Support\Arr;
use Yormy\TranslationcaptainLaravel\Services\FileTypes\FileTypeJson;

class ReaderVue extends FileReader
{
    /**
     * App translations
     * lang/<language>/directory/directory/translations.json
     */
    const APP_FILES = 1;

    public function __construct(array $locales)
    {
        $this->filetype = new FileTypeJson();

        $this->dataBindingPattern ="{(.*?)}";

        parent::__construct($locales);
    }

    public function getMessages()
    {
        foreach ($this->locales as $locale) {
            $importFromLanguageDir = $this->importPath . DIRECTORY_SEPARATOR . $locale;
            $this->importFileTranslations(self::APP_FILES, $importFromLanguageDir, $importFromLanguageDir, $locale);
        }

        return $this->messages;
    }


    public function addSingleTranslationFiles(int $directoryType, string $fullPathname, string $root, string $language = null)
    {
        if (!is_file($fullPathname)) {
            return;
        }

        $relative = $this->getBareFilename($root, $fullPathname);

        $keysForPackage = [];
        $translations = $this->convertImportfileToArray($fullPathname);

        if (count($translations) > 0) {
            foreach ($translations as $key => $translation) {
                $keysForPackage[$key] = $this->processTranslation($translation);
            }

            $this->messages[$language][$relative] = $keysForPackage;
        }
    }

    protected function processTranslation(string $translation) : string
    {
        $translation = $this->createNewDataBinding($translation);
        return $translation;
    }

    /**
     * Convert the php array structure text file into an php array object with dot notations
     *
     * @param string $filename
     * @return array
     */
    public function convertImportfileToArray(string $filename) : array
    {
        $arrayTranslations = json_decode(file_get_contents($filename), true);

        if(is_array($arrayTranslations)) {
            return Arr::dot($arrayTranslations);  // make single dimensional array
        }
        return [];
    }

    protected function getRawDataBinding($value)
    {
        return '{'. $value. '}';
    }
}

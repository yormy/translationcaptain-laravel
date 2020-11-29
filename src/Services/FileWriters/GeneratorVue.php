<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileWriters;

use Yormy\TranslationcaptainLaravel\Services\FileTypes\FileTypeJson;

class GeneratorVue extends FilesGenerator
{
    protected $vendorPath = 'vendor';

    public function __construct(array $labels)
    {
        $this->exportSettings = new ExportSettingsJson();

        $this->filetype = new FileTypeJson();

        $this->exportPath = App()['path.lang'];
        $this->exportPath .= '_tc_vuw';

        parent::__construct($labels);
    }

    public function export(array $locales = null)
    {
        if (!$locales) {
            $locales = array_keys($this->labels);
        }

        foreach ($locales as $locale) {
            $filesToExport = $this->prepareExport($locale);
            $this->generateFiles($filesToExport);
        }
    }

    protected function groupnameToFilename(string $groupName, string $locale): string
    {
//        if ($this->isVendorKey($groupName))
//        {
//            $vendorSeparatorPosition = strpos($groupName,self::VENDORNAME_SEPARATOR);
//            $vendorName = substr($groupName, 0, $vendorSeparatorPosition);
//            $filename = substr($groupName, $vendorSeparatorPosition + strlen(self::VENDORNAME_SEPARATOR) , strlen($groupName));
//            return $this->vendorPath.
//                DIRECTORY_SEPARATOR. $vendorName.
//                DIRECTORY_SEPARATOR. $locale.
//                DIRECTORY_SEPARATOR.  $filename. $this->filetype->extension;
//        }

        return $locale. DIRECTORY_SEPARATOR.  $groupName. $this->filetype->extension;
    }

    protected function prepareTranslationForExport(string $translation) : string
    {
        $translation = addslashes($translation);

        return parent::prepareTranslationForExport($translation);
    }

    protected function processMessage(string $message) : string
    {
        $jsonMessage = json_encode($message);
        $jsonMessage = $this->processDataBinding($jsonMessage);

        return json_decode($jsonMessage, true);
    }

    protected function makeRawDataBinding($value) : string
    {
        return '{'. $value. '}';
    }
}

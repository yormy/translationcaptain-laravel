<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\Generators;

class VueFilesGenerator extends FilesGenerator
{

    protected $filesToExport;

    protected $vendorPath = 'vendor';

    protected $filetype;


    public function __construct(array $labels)
    {
        $this->settings = new ExportSettingsJson();

        $this->filetype = new FileTypeJson();

        $this->exportPath = App()['path.lang'];
        $this->exportPath .='_tc_vuw';

        parent::__construct($labels);
    }


    public function export(array $locales)
    {
//            $this->exportPath = $this->exportRoot . DIRECTORY_SEPARATOR . "blade";
//            //$this->zipFilename = "laravel";

        foreach ($locales as $locale) {
            $this->prepareExport($locale);
            $this->generateFiles();
        }
    }

    protected function groupnameToFilename(string $groupName, string $locale): string
    {
        if ($this->isVendorKey($groupName))
        {
            $vendorSeparatorPosition = strpos($groupName,self::VENDORNAME_SEPARATOR);
            $vendorName = substr($groupName, 0, $vendorSeparatorPosition);
            $filename = substr($groupName, $vendorSeparatorPosition + strlen(self::VENDORNAME_SEPARATOR) , strlen($groupName));
            return $this->vendorPath.
                DIRECTORY_SEPARATOR. $vendorName.
                DIRECTORY_SEPARATOR. $locale.
                DIRECTORY_SEPARATOR.  $filename. $this->filetype->extension;
        }

        return $locale. DIRECTORY_SEPARATOR.  $groupName. $this->filetype->extension;
    }

    protected function generateFileContents(array $translations) : string
    {
        $contents = "";
        foreach ($translations as $key => $value) {
            if ($contents) {
                $contents .= $this->filetype->eol;
            }

            $contents .= PHP_EOL. $this->filetype->tab;
            $contents .= $this->settings->quote. $key. $this->settings->quote;
            $contents .= $this->settings->keyToValue;

            $result = "";
//            $level = 2;
//            if (is_array($value)) {
//                $this->arrayToText($value, $result, $level);
//
//                $fileLines .= $objectOpen. $result;
//                $fileLines .= PHP_EOL. $this->tab. $objectClose;
//            } else {
            $contents .= $this->settings->quote. $value. $this->settings->quote;
//            }
        }

        return $contents;
    }

    protected function prepareTranslationForExport(string $translation) : string
    {
        $translation = addslashes($translation);
        return $translation;
    }
}

<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\Generators;

use Illuminate\Support\Facades\Storage;

class BladeFilesGenerator extends FilesGenerator
{

    protected $vendorPath = 'vendor';

    protected $filetype;

    public function __construct(array $labels)
    {
        $this->settings = new ExportSettingsPhp();

        $this->filetype = new FileTypePhp();

        $this->exportPath = App()['path.lang'];
        $this->exportPath .='_tc';

        parent::__construct($labels);
    }


    public function export(array $locales)
    {
//            $this->exportPath = $this->exportRoot . DIRECTORY_SEPARATOR . "blade";
//            //$this->zipFilename = "laravel";

        foreach ($locales as $locale) {
            $filesToExport = $this->prepareExport($locale);
            $this->generateFiles($filesToExport);

//            $roots = $this->prepareForBlade($locale);
//
//            if ($this->exportFormat === self::AS_ARRAY) {
//                $this->generate($roots, "php");
//            }
        }
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

            $contents .= $this->settings->quote. $value. $this->settings->quote;
        }

        return $contents;
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






}

<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileWriters;


use Yormy\TranslationcaptainLaravel\Services\FileTypes\FileTypePhp;

class GeneratorBlade extends FilesGenerator
{

    protected $vendorPath = 'vendor';

    public function __construct(array $labels)
    {
        $this->exportSettings = new ExportSettingsPhp();

        $this->filetype = new FileTypePhp();

        $this->exportPath = App()['path.lang'];
        $this->exportPath .='_tc';

        parent::__construct($labels);
    }


    public function export(array $locales)
    {
        foreach ($locales as $locale) {
            $filesToExport = $this->prepareExport($locale);
            $this->generateFiles($filesToExport);
        }
    }

    protected function groupnameToFilename(string $groupName, string $locale): string
    {
        if ($groupName == config('translationcaptain-laravel.group_when_group_missing')) {
            return $locale. $this->filetype->extension; // write as en.php
        }

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

    protected function processMessage(string $message) : string
    {
        return $this->processDataBinding($message);
    }

    protected function makeRawDataBinding($value)
    {
        return ':'. $value;
    }
}

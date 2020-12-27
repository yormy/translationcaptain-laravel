<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileWriters;

use Yormy\TranslationcaptainLaravel\Services\FileTypes\FileTypePhp;

class WriterBlade extends FileWriter
{
    protected $vendorPath = 'vendor';

    public function __construct()
    {
        $this->exportSettings = new ExportSettingsPhp();

        $this->filetype = new FileTypePhp();

        $this->exportPath = App()['path.lang'];
        $this->exportPath .= '_tc';

        parent::__construct();
    }

    public function export(array $locales = null)
    {
        if (! $locales) {
            $locales = array_keys($this->labels);
        }

        foreach ($locales as $locale) {
            $filesToExport = $this->prepareExport($locale);
            $this->generateFiles($filesToExport);
        }
    }

    protected function groupnameToFilename(string $groupName, string $locale): string
    {
        if ($groupName == config('translationcaptain.group_when_group_missing')) {
            return $locale. $this->filetype->extension; // write as en.php
        }

        if ($this->isVendorKey($groupName)) {
            $vendorSeparatorPosition = strpos($groupName, self::VENDORNAME_SEPARATOR);
            $vendorName = substr($groupName, 0, $vendorSeparatorPosition);
            $filename = substr($groupName, $vendorSeparatorPosition + strlen(self::VENDORNAME_SEPARATOR), strlen($groupName));

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

    protected function makeRawDataBinding($value) : string
    {
        return ':'. $value;
    }
}

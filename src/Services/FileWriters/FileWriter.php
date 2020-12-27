<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileWriters;

use Yormy\TranslationcaptainLaravel\Exceptions\InvalidSetupException;

abstract class FileWriter
{
    const VENDORNAME_SEPARATOR = '::';

    protected array $labels;

    protected string $exportPath;

    protected $header = "______GENERATED BY TRANSLATION CAPTAIN______";

    protected $exportSettings;

    protected $filetype;

    private $zipFilenamePrefix = "backup-";

    public function __construct()
    {
        $this->header = "______GENERATED BY TRANSLATION CAPTAIN______ (". date('Y-m-d H:m:s') .")";
    }

    public function setLabels(array $labels)
    {
        $this->labels = $labels;
    }

    abstract protected function groupnameToFilename(string $groupName, string $locale): string;

    abstract protected function processMessage(string $message) : string;

    abstract protected function makeRawDataBinding($value) : string;

    public function setHeader(string $header) : self
    {
        $this->header = $header;

        return $this;
    }

    public function zipCurrentFiles()
    {
        $this->zipping();
    }

    public function setExportPath(string $pathname) : self
    {
        $this->exportPath = $pathname;

        return $this;
    }

    protected function prepareExport(string $locale) : array
    {
        if (! $this->labels) {
            throw new InvalidSetupException('Labels not set');
        }

        $groups = $this->labels[$locale];

        $filesToExport = [];
        foreach ($groups as $groupname => $keys) {
            $filename = $this->groupnameToFilename($groupname, $locale);

            foreach ($keys as $key => $translation) {
                $keyToExport = $this->prepareKeyForExport($key);
                $translationToExport = $this->prepareTranslationForExport($translation);
                $filesToExport[$filename][$keyToExport] = $translationToExport;
            }
        }

        return $filesToExport;
    }

    protected function generateFiles(array $filesToExport)
    {
        foreach ($filesToExport as $filename => $translations) {
            $fullpath = $this->exportPath. DIRECTORY_SEPARATOR. $filename;

            $fileContents = "";
            $fileContents .= $this->filetype->getFileStart($this->header);
            $fileContents .= $this->generateFileContents($translations);
            $fileContents .= $this->filetype->getFileEnd();

            $this->writeFile($fullpath, $fileContents);
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
            $contents .= $this->exportSettings->quote. $key. $this->exportSettings->quote;
            $contents .= $this->exportSettings->keyToValue;

            $contents .= $this->exportSettings->quote. $value. $this->exportSettings->quote;
        }

        return $contents;
    }

    protected function isVendorKey(string $groupName)
    {
        return strpos($groupName, self::VENDORNAME_SEPARATOR) > 0;
    }

    protected function prepareKeyForExport(string $key) : string
    {
        return $key;
    }

    protected function prepareTranslationForExport(string $translation) : string
    {
        return $this->processMessage($translation);
    }

    private function writeFile(string $fullpath, string $fileContents)
    {
        if (! file_exists(dirname($fullpath))) {
            mkdir(dirname($fullpath), 0660, true);
        }

        echo getcwd();
        echo json_encode(scandir(getcwd(). "/tests"));
        echo substr(sprintf('%o', fileperms(getcwd(). "/tests")), -4);
        die();
        file_put_contents($fullpath, $fileContents);
    }

    private function zipping()
    {
        $timestamp = date('Y-m-d_H:m');
        $zipFilename = $this->exportPath. DIRECTORY_SEPARATOR. $this->zipFilenamePrefix. "$timestamp.zip";

        $backupDirectory = App()['path.lang'];

        $zip = new \ZipArchive();
        $zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($backupDirectory));
        foreach ($files as $file) {
            // We're skipping all subfolders
            if (! $file->isDir()) {
                $filePath = $file->getRealPath();

                $relativePath = '' . substr($filePath, strlen($backupDirectory) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    }

    protected function processDataBinding(string $message) : string
    {
        $start = (string)config('translationcaptain.databinding.start');
        $end = (string)config('translationcaptain.databinding.end');
        $pattern = "$start(.*?)$end";

        preg_match_all("/$pattern/", $message, $matches);
        if ($matches) {
            $exactFind = $matches[0];
            $innerFind = $matches[1];

            foreach ($innerFind as $value) {
                $binding = $this->makeRawDataBinding($value);
                $message = str_ireplace($exactFind, $binding, $message);
            }
        }

        return $message;
    }
}

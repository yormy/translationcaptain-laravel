<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\Generators;

use Illuminate\Support\Facades\Storage;

class BladeFilesGenerator extends FilesGenerator
{

    protected $filesToExport;

    protected $vendorPath = 'vendor';

    const VENDORNAME_SEPARATOR = '::';

    const FILE_EOL = ',';

    const FILE_TAB = '    ';


    public function __construct(array $labels)
    {
        $this->settings = new ArraySettings();

        $this->exportPath = App()['path.lang'];
        $this->exportPath .='_tc';

        parent::__construct($labels);
    }


    public function export(array $locales)
    {
//            $this->exportPath = $this->exportRoot . DIRECTORY_SEPARATOR . "blade";
//            //$this->zipFilename = "laravel";

        foreach ($locales as $locale) {
            $this->prepareExport($locale);
            $this->generateFiles();

//            $roots = $this->prepareForBlade($locale);
//
//            if ($this->exportFormat === self::AS_ARRAY) {
//                $this->generate($roots, "php");
//            }
        }
    }

    private function generateFiles()
    {
        foreach ($this->filesToExport as $filename => $translations) {
            $fullpath = $this->exportPath. DIRECTORY_SEPARATOR. $filename;

            $fileContents = "";
            $fileContents .= $this->setFileStart();
            $fileContents .= $this->generateFileContents($translations);
            $fileContents .= $this->setFileEnd();

            $this->writeFile($fullpath, $fileContents);
        }
    }

    private function writeFile(string $fullpath , string $fileContents)
    {
        if (!file_exists(dirname($fullpath))) {
            mkdir(dirname($fullpath), 0660, true);
        }

        file_put_contents($fullpath, $fileContents);
    }

    private function generateFileContents(array $translations) : string
    {
        $contents = "";
        foreach ($translations as $key => $value) {
            if ($contents) {
                $contents .= self::FILE_EOL;
            }

            $contents .= PHP_EOL. self::FILE_TAB;
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



    private function generateOrg($roots, $fileExtension = "php")
    {
        $quote = $this->quote;
        $keyToValue  = $this->keyToValue;
        $objectOpen  = $this->objectOpen;
        $objectClose  = $this->objectClose;

        foreach ($roots as $rootItem => $files) {
            foreach ($files as $fileName => $translations) {

                $filename = $rootItem. DIRECTORY_SEPARATOR. $fileName;
                $filename .= ".". $fileExtension;

                $fileLines = "";
                foreach ($translations as $key => $value) {
                    if ($fileLines) {
                        $fileLines .= ",";
                    }

                    $fileLines .= PHP_EOL. $this->tab;
                    $fileLines .= $quote. $key. $quote;
                    $fileLines .= $keyToValue;

                    $result = "";
                    $level = 2;
                    if (is_array($value)) {
                        $this->arrayToText($value, $result, $level);

                        $fileLines .= $objectOpen. $result;
                        $fileLines .= PHP_EOL. $this->tab. $objectClose;
                    } else {
                        $fileLines .= $quote. $value. $quote;
                    }
                }

                $fileContents = "";
                $fileContents .= $this->fileOpen;
                $fileContents .= $fileLines;
                $fileContents .= $this->fileClose;
                dd($this->exportPath. DIRECTORY_SEPARATOR. $filename);
                Storage::put($this->exportPath. DIRECTORY_SEPARATOR. $filename, $fileContents);
            }
        }
    }


    private function prepareExport(string $locale)
    {
        $groups = $this->labels[$locale];

        foreach ($groups as $groupname => $keys) {
            $filename = $this->groupnameToFilename($groupname , $locale);

            foreach ($keys as $key => $translation)
            {
                $keyToExport = $this->prepareKeyForExport($key);
                $translationToExport = $this->prepareTranslationForExport($translation);
                $this->filesToExport[$filename][$keyToExport] = $translationToExport;
            }
        }
    }

    private function groupnameToFilename(string $groupName, string $locale): string
    {
        $extension = '.php';
        if ($this->isVendorKey($groupName))
        {
            $vendorSeparatorPosition = strpos($groupName,self::VENDORNAME_SEPARATOR);
            $vendorName = substr($groupName, 0, $vendorSeparatorPosition);
            $filename = substr($groupName, $vendorSeparatorPosition + strlen(self::VENDORNAME_SEPARATOR) , strlen($groupName));
            return $this->vendorPath. DIRECTORY_SEPARATOR. $vendorName. DIRECTORY_SEPARATOR. $locale. DIRECTORY_SEPARATOR.  $filename. $extension;
        }

        return $locale. DIRECTORY_SEPARATOR.  $groupName. $extension;
    }

    private function isVendorKey(string $groupName)
    {
        return strpos($groupName, self::VENDORNAME_SEPARATOR) > 0;
    }

    private function prepareTranslationForExport(string $translation) : string
    {
        return "#".$translation;
    }

    private function prepareKeyForExport(string $key) : string
    {
        return "#".$key;
    }


    private function setFileStart()
    {
        $open = "";
        $open .= "<?php " . PHP_EOL . PHP_EOL;
        $open .= "//" . $this->header . PHP_EOL;
        $open .= "return [";

        return $open;
    }

    private function setFileEnd()
    {
        return PHP_EOL . "];" . PHP_EOL;
    }




}

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

            $contents .= $this->settings->quote. $value. $this->settings->quote;
        }

        return $contents;
    }

    protected function prepareTranslationForExport(string $translation) : string
    {
        $translation = addslashes($translation);

        return $this->processMessage($translation);
    }

    protected function processMessage(string $message) : string
    {
        $jsonMessage = json_encode($message);
        $jsonMessage = $this->processDataBindingVue($jsonMessage);
        return json_decode($jsonMessage, true);
    }

    protected function processDataBindingVue(string $message) : string
    {
        $endingChars = "\.|;|:| |@|\(|\)";
        $pattern = ":([a-zA-Z]+?)($endingChars)";

        preg_match_all("/$pattern/", $message, $matches);
        if ($matches) {
            //$exactFind = $matches[0];
            $innerFind = $matches[1];

            foreach ($innerFind as $value) {
                $laravelBinding = ":$value";
                $vueBinding = "{". $value. "}";
                $message = str_ireplace($laravelBinding, $vueBinding, $message);
            }
        }
        return $message;
    }
}

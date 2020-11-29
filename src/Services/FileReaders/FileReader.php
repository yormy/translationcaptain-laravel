<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileReaders;

use Illuminate\Filesystem\Filesystem;

abstract class FileReader
{
    protected $locales;

    protected $importPath;

    protected $fileSystem;

    protected array $messages = [];

    protected $filetype;

    protected $dataBindingPattern;

    abstract public function addSingleTranslationFiles(int $directoryType, string $fullPathname, string $root, string $language = null): void;

    abstract protected function getRawDataBinding($value) : string;

    public function __construct(array $locales)
    {
        $this->locales = $locales;

        $this->fileSystem = new Filesystem();
    }

    public function setImportPath(string $importPath) : self
    {
        $this->importPath = $importPath;

        return $this;
    }

    public function getBareFilename(string $root, string $fullPathname) : string
    {
        $relative = str_replace($root, '', $fullPathname);

        // strip leading directory separator
        $first = substr($relative, 0, 1);
        if ($first === DIRECTORY_SEPARATOR) {
            $relative = substr($relative, 1, strlen($relative));
        }

        return substr($relative, 0, strlen($relative) - strlen($this->filetype->extension));
    }

    /**
     * Recursive walk the directory tree and import the files according to
     * the laravel base structure: lang/<language>/group/group.php file stucture
     *
     * @param $root
     * @param $baseDir
     */
    public function importFileTranslations(int $directoryType, string $root, string $importFromDir, string $language = null) : void
    {
        if (! is_dir($importFromDir)) {
            return;
        }

        $this->addTranslationFiles($directoryType, $root, $importFromDir, $language);
        foreach ($this->fileSystem->directories($importFromDir) as $currentDir) {
            $this->importFileTranslations($directoryType, $root, $currentDir, $language);
        }
    }

    public function addTranslationFiles(int $directoryType, string $root, string $importFromDir, string $language = null)
    {
        foreach ($this->fileSystem->files($importFromDir) as $file) {
            $fullPathname = $file->getPathname();
            $this->addSingleTranslationFiles($directoryType, $fullPathname, $root, $language);
        }
    }

    protected function processTranslation(string $translation) : string
    {
        return $translation;
    }

    protected function createNewDataBinding(string $translation) : string
    {
        preg_match_all("/$this->dataBindingPattern/", $translation, $matches);
        if ($matches) {
            $innerFind = $matches[1];

            foreach ($innerFind as $value) {
                $bladeBinding = $this->getRawDataBinding($value);
                $TranslationCaptainBinding =
                    config('translationcaptain-laravel.databinding.start')
                    . $value
                    . config('translationcaptain-laravel.databinding.end');
                $translation = str_ireplace($bladeBinding, $TranslationCaptainBinding, $translation);
            }
        }

        return $translation;
    }

    protected function fixEmptyArray(array $keyValues) : array
    {
        // Arr::dot convert an empty array not to a dotted value but remains an empty array.
        // Remove this empty array so we can trust on a single dimensional array
        foreach ($keyValues as $key => $value) {
            if (is_array($value)) {
                unset($keyValues[$key]);
            }
        }

        return $keyValues;
    }
}

<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SearchSources
{

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fileSystem;

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    protected array $messages;

    /**
     * Translation function pattern.
     *
     * @var string
     */
    private $pattern = '/(__)\([\'"](.+)[\'"][\),]/U';

    public function getMessages()
    {
        $files = $this->getAllFilesToProcess();

        $allStrings = $this->collectStrings($files);

        $allStrings = $this->formatGroupKey($allStrings);

        return $allStrings;
    }

    private function formatGroupKey(array $allStrings)
    {
        $string = [];
        foreach ($allStrings as $fullkey => $translation) {
            $firstDotSeparator = (int)strpos($fullkey, ".");

            if ($firstDotSeparator > 0) {
                $filename = substr($fullkey, 0, $firstDotSeparator);
                $key = substr($fullkey, $firstDotSeparator + 1, strlen($fullkey));

                $string[$filename][$key] = $translation;
            } else {
                $defaultGroup = config('translationcaptain.group_when_group_missing');
                $string[$defaultGroup][$fullkey] = $translation;
            }
        }

        return $string;
    }

    /**
     * Traverse all paths and collect filenames
     *
     * @return array
     */
    private function getAllFilesToProcess()
    {
        $files = [];
        $sources = config('translationcaptain.source_code_scan_paths.blade');

        foreach ($sources as $path) {
            $absPath = base_path() . $path;

            // create an array with all processable files
            $this->getAllFiles($absPath, $files);
        }


        $queueFilename = (string)config('translationcaptain.queue_filename');
        if (Storage::exists($queueFilename)) {
            $files[] = Storage::path($queueFilename);
        }

        return $files;
    }

    /**
     * Parse all files and store keys in return array
     *
     * @param array $files
     * @return array
     */
    private function collectStrings(array $files)
    {
        $allStrings = [];
        foreach ($files as $filePath) {
            // Get translatable strings in the given view
            $currentStrings = $this->getTranslatable(File::get($filePath));

            if (is_array($currentStrings)) {
                $allStrings = array_merge($allStrings, $currentStrings);
            }
        }

        return $allStrings;
    }

    /**
     * Get all the files that need to be parsed
     *
     * @param $dir
     * @param array $results
     * @return array
     */
    private function getAllFiles($dir, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (! is_dir($path)) {
                $results[] = $path;
            } elseif ($value != "." && $value != "..") {
                $this->getAllFiles($path, $results);
            }
        }

        return $results;
    }

    /**
     * Parse a file in order to find translatable strings.
     *
     * @param string $viewData
     * @return array
     */
    private function getTranslatable(string $viewData)
    {
        $strings = [];
        if (! preg_match_all($this->pattern, $viewData, $matches)) {
            return $strings;
        }
        foreach ($matches[2] as $string) {
            $strings[] = $string;
        }
        // Remove duplicates.
        $strings = array_unique($strings);

        return $this->formatArray($strings);
    }

    /**
     * Convert an array of extracted strings to an associative array where each string becomes key and value.
     *
     * @param array $strings
     * @return array
     */
    private function formatArray(array $strings)
    {
        $result = [];
        foreach ($strings as $string) {
            $result[$string] = '';
        }

        return $result;
    }
}

<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SearchSources
{

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fileSystem;

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;
//
    protected array $messages;
//
//    protected array $languages;
//
    protected $defaultGroup = "basedefault";
//
//    /**
//     * Published vendor translations
//     * lang/vendor/<package-name>/<language>/directory/directory/translations.php
//     */
//    const VENDOR_FILES = 0;
//
//    /**
//     * App translations
//     * lang/<language>/directory/directory/translations.php
//     */
//    const APP_FILES = 1;
//
//    /**
//     * App translations
//     * lang/en.php
//     */
//    const APP_SINGLE_FILES = 2;
//

    /**
     * The target directory for translation files.
     *
     * @var string
     */
    private const TRANSLATION_FILE_DIRECTORY = 'resources/lang';

    /**
     * Default language for translation files
     */
    private const DEFAULT_LANG = 'en';

    /**
     * @var string
     */
//    private $path;

    private const PATHS_TO_SCAN = [
        //'/app/',
        //'/config/',
        '/resources/views/bedrock/admin',
    ];

    /**
     * Translation function pattern.
     *
     * @var string
     */
    private $pattern = '/(__)\([\'"](.+)[\'"][\),]/U';


//    public function __construct()
//    {
////        $this->app = App();
////        $this->fileSystem = new Filesystem();
////
////        $this->languages =['en','nl','ar'];
//    }

    public function getMessages()
    {
//
//        dd('ooo');
//        $path = $this->argument('path');
//
//        if ($path) {
//            $files = $this->getPathFilesToProcess($path);
//        } else {
//            $files = $this->getAllFilesToProcess();
//        }

        $files = $this->getAllFilesToProcess();



        $allStrings = $this->collectStrings($files);


        $allStrings = $this->formatGroupKey($allStrings);

        return $allStrings;

//        $currentTranslatedStrings = $this->getCurrentDefaultTranslations();
//
//        // Merge the view strings with the
//        $mergedStrings = array_merge($allStrings, $currentTranslatedStrings);
//        ksort($mergedStrings, SORT_FLAG_CASE | SORT_NATURAL);
//
//        $missingInCurrentLanguage = array_diff_key($mergedStrings, $currentTranslatedStrings);
//
//        // Check if there are new changes detected
//        if (count($missingInCurrentLanguage) === 0) {
//            $this->info('No new translatable strings found. Exiting...');
//            return 0;
//        }
//
//        $this->info('Found the following new translation strings:');
//
//        $this->line('--------');
//        foreach ($missingInCurrentLanguage as $key => $diff) {
//            $this->info($key);
//            $this->line('--------');
//        }
//
//        if ($this->confirm('Would you like to add this values to ' . self::DEFAULT_LANG . '.json?', false)) {
//            File::put(
//                $this->findTranslationPath(),
//                json_encode($mergedStrings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
//            );
//            $this->info('Translations written to file!');
//        } else {
//            $this->info('Operation cancelled.');
//        }
//        return 0;
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
                $string["??"][$fullkey] =  $translation;
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
        foreach (self::PATHS_TO_SCAN as $path) {
            $absPath = base_path() . $path;

            // create an array with all processable files
            $this->getAllFiles($absPath, $files);
        }
        return $files;
    }

    private function getPathFilesToProcess($path)
    {
        $files = [];
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        $absPath = base_path() . $path;

        // create an array with all processable files
        $this->getAllFiles($absPath, $files);

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
        foreach ($files as $key => $filePath) {
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

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ($value != "." && $value != "..") {
                $this->getAllFiles($path, $results);
            }
        }

        return $results;
    }

    /**
     * Get the keys from the json
     *
     * @return mixed
     */
    private function getCurrentDefaultTranslations()
    {
        try {
            $languageFileContent = File::get($this->findTranslationPath());
        } catch (FileNotFoundException $e) {
            $this->error('Could not find the translations file for language' . self::DEFAULT_LANG);
            exit(1);
        }
        return json_decode($languageFileContent, true);
    }

    private function findTranslationPath()
    {
        return base_path() . DIRECTORY_SEPARATOR .
            self::TRANSLATION_FILE_DIRECTORY . DIRECTORY_SEPARATOR .
            self::DEFAULT_LANG . '.json';
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
        if (!preg_match_all($this->pattern, $viewData, $matches)) {
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

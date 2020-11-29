<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileTypes;

class FileTypeJson
{
    public $eol = ',';

    public $tab = '    ';

    public $extension = ".json";

    public function getFileStart(string $header)
    {
        $open = "";
        $open .= "{ " . PHP_EOL . PHP_EOL;
        $open .= $this->tab. '"'. $header. '" : "",'. PHP_EOL;

        return $open;
    }

    public function getFileEnd()
    {
        return PHP_EOL . "}" . PHP_EOL;
    }
}

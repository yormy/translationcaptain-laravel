<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services\FileTypes;

class FileTypePhp
{
    public $eol = ',';

    public $tab = '    ';

    public $extension = ".php";

    public function getFileStart(string $header)
    {
        $open = "";
        $open .= "<?php " . PHP_EOL . PHP_EOL;
        $open .= "//" . $header . PHP_EOL;
        $open .= "return [";

        return $open;
    }

    public function getFileEnd()
    {
        return PHP_EOL . "];" . PHP_EOL;
    }
}

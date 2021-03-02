<?php

namespace App\classes\FileCoder;

use Illuminate\Support\Str;

class PhpFileCoder extends FileCoderAbstract
{
    protected function file_recoding(string $path): array
    {
        try {
            if(file_exists($path)) {
                $file = file_get_contents($path);
                if($file[0] ?? '' == '<') {
                    return include ($path);
                }
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function set(array $indexed, $value): FileCoderAbstract
    {
        $this->setter($indexed, $value, $this->items);
        return $this;
    }

    public function save()
    {
        $data = "<?php\n\nreturn ".var_export($this->items, true).';';
        $data = str_replace(["array (\n", "\n);", "),\n", "=> \n", " =>   "],["[\n", "\n];", "],\n", "=> ", " => "],$data);

        file_put_contents($this->path, $data);
    }
}

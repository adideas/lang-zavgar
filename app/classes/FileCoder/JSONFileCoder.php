<?php

namespace App\classes\FileCoder;

class JSONFileCoder extends FileCoderAbstract
{
    protected function file_recoding(string $path): array
    {
        if(file_exists($path)) {
            $data = file_get_contents($path);
            try {
                return (array)json_decode(str_replace(['export default ', "\r\n"], ['', ''], $data), true);
            } catch (\Exception $e) {
                return [];
            }

        }
        return [];
    }

    public function set(array $indexed, $value): FileCoderAbstract
    {
        $this->setter($indexed, $value, $this->items);

        return $this;
    }

    public function save()
    {
        $data = json_encode($this->items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($this->path, $data);
    }
}

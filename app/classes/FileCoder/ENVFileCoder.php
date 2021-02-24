<?php

namespace App\classes\FileCoder;

class ENVFileCoder extends FileCoderAbstract
{
    protected function file_recoding(string $path): array
    {
        $rebuild_data = [];

        $data = str_replace(["\r", "\t"], ['', ''], file_get_contents($path));
        $data = explode("\n", $data);
        foreach ($data as $_ => $item) {
            preg_match_all('/(.*)=(.*)/', $item, $matches);
            if (count($matches) == 3) {
                if (isset($matches[1][0], $matches[2][0])) {
                    $rebuild_data[$matches[1][0]] = $matches[2][0];
                }
            }
        }

        return $rebuild_data;
    }

    public function set(array $indexed, $value): FileCoderAbstract
    {
        $this->setter($indexed, $value, $this->items);

        return $this;
    }

    public function save()
    {
        $data = urldecode(str_replace('&', "\n", http_build_query($this->items)));

        file_put_contents($this->path, $data);
    }
}

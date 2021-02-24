<?php

namespace App\classes\FileCoder;

class NOPUBLISHFileCoder extends FileCoderAbstract
{
    protected function file_recoding(string $path): array
    {
        return [];
    }

    public function set(array $indexed, $value): FileCoderAbstract
    {
        return $this;
    }

    public function save()
    {
    }
}

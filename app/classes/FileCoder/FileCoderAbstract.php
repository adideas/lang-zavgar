<?php

namespace App\classes\FileCoder;

abstract class FileCoderAbstract
{
    protected array $items;

    protected string $path;

    abstract protected function save();

    abstract protected function file_recoding(string $path): array;

    abstract protected function set(array $indexed, $value): FileCoderAbstract;

    public function __construct(string $path = '')
    {
        $this->path = $path;
        $this->items = $this->file_recoding($path);
    }

    protected function setter(array $indexed, $value, &$items) {
        $index = $indexed[0];
        $indexed  = array_slice($indexed, 1);
        if (count($indexed)) {
            if (isset($items[$index])) {
                $this->setter($indexed, $value, $items[$index]);
            } else {
                if(is_array($items)) {
                    $items = array_replace((array)$items, [ $index => $value ]);
                } else {
                    $items = [ $index => $value ];
                }
                $this->setter($indexed, $value, $items[$index]);
            }
        } else {
            if (isset($items[$index])) {
                $items[$index] = $value;
            } else {
                if(is_array($items)) {
                    $items = array_replace((array)$items, [ $index => $value ]);
                } else {
                    $items = [ $index => $value ];
                }
            }
        }
    }
}

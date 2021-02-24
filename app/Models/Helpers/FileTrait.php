<?php

namespace App\Models\Helpers;

use App\Models\File;
use App\Models\Language;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    private function convertPath(string $path, Language $language) : string {
        $path = implode(DIRECTORY_SEPARATOR, explode('/', $path));

        $path = str_replace([DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, '${language}'], [DIRECTORY_SEPARATOR, $language->name], storage_path($path));

        return $path;
    }

    public function checkFile(File $file, Language $language, $return = true) : string
    {
        if(!$language->publish) {
            return '';
        }
        $path = $this->convertPath($file->path, $language);

        if(storage_path() != $path) {
            if (!file_exists($path)) {
                $this->checkFile($file->parent_file, $language, false);
                if ($file->is_file) {
                    file_put_contents($path, '');
                } else {
                    mkdir($path);
                }
            }
        }
        if ($return) {
            return $path;
        } else {
            return '';
        }
    }
}

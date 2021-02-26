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

    public function rePathChildrenFile(File $file) {

        $new_path = str_replace('//','/',"{$file->parent_file->path}/{$file->name}");
        if ($file->is_file) {
            $file->path = $new_path . '.'. strtolower($file->type->name);
        } else {
            $file->path = $new_path;
        }
        $file->save();

        function file_child_rename($children, $parent_path) {
            foreach ($children as $key => $child) {
                $parent_path = "{$parent_path}/{$child->name}";

                if ($child->is_file) {
                    $child->path = $parent_path . '.'. strtolower($child->type->name);
                } else {
                    $child->path = $parent_path;
                }

                $child->save();

                $children1 = $child->children;
                if ($children1) {
                    file_child_rename($children1, $parent_path);
                }

            }
        }


        $children = $file->children;
        if ($children) {
            file_child_rename($children, $new_path);
        }
    }

    public function syncFolder() {
        $root_file = File::where('root', 1)->first();
        $storage_dir = \Illuminate\Support\Facades\File::allFiles(storage_path($root_file->name));
        $files = [];
        foreach ($storage_dir as $key => $file) {
            $files[] = DIRECTORY_SEPARATOR .$root_file->name.DIRECTORY_SEPARATOR.strtolower($file->getRelativePathname());
        }

        Language::each(
            function (Language $language) use (&$files) {
                File::where('is_file', 1)->each(
                  function (File $file) use (&$files, $language) {
                      $path = $this->checkFile($file, $language);
                      $path = str_replace(storage_path(), '', $path);
                      $files = array_filter($files, fn($x) => $x != $path);
                  }
                );
            }
        );

        dump($files);

        foreach ($files as $_ => $file) {
            \Illuminate\Support\Facades\File::delete(storage_path($file));
        }
    }
}

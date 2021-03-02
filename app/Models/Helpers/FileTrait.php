<?php

namespace App\Models\Helpers;

use App\Models\File;
use App\Models\Key;
use App\Models\Language;
use App\Models\Translate;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    // конвертация в абсолютный путь
    public function convertPath(string $path, Language $language) : string {
        $path = implode(DIRECTORY_SEPARATOR, explode('/', $path));

        $path = str_replace([DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, '${language}'], [DIRECTORY_SEPARATOR, $language->name], storage_path($path));

        return $path;
    }

    // проверка наличия файла и если его нет он будет создан
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

    // рекурсивная замена индекса ключа
    public function reIndexedChildrenKey(Key $key) {
        function renKeys($key, $parent) {
            if ($key->parent) {
                $key->indexed = [...($parent->indexed ?? json_decode($parent->indexed, true)), $key->name];

                $key->save();
                if ($key->keys && count($key->keys)) {
                    foreach ($key->keys as $_ => $key_) {
                        renKeys($key_, $key);
                    }
                }
            }
        }

        if ($key->keys && count($key->keys)) {
            foreach ($key->keys as $_ => $key_) {
                renKeys($key_, $key);
            }
        }
    }

    // индексация файла
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

    // выгрузка конкретного файла
    public function exportFile(File $file) {
        $keys_translation = [];
        $export           = [];

        Translate::where('file_id', $file->id)->each(
            function (Translate $translate) use (&$keys_translation, &$export) {
                if (!count($keys_translation)) {
                    $keys_translation = array_values(array_filter($translate->getFillable(), fn($x) => intval($x)));
                    foreach ($keys_translation as $key => $item) {
                        $export[$item] = [];
                    }
                }
                $indexed = $translate->key->indexed;
                foreach ($keys_translation as $key => $item) {
                    $value = $translate->{$item};
                    if ($value) {
                        array_push($export[$item], [$value => $indexed]);
                    }
                }
            }
        );

        $class_coder = $file->type->class_coder;

        Language::each(
            function (Language $language) use ($file, $class_coder, $export) {
                $path  = $this->checkFile($file, $language);
                $class = new $class_coder($path);
                $class->clear();
                $data = $export['0' . $language->id] ?? [];
                foreach ($data as $i => $export_item) {
                    foreach ($export_item as $value => $indexed) {
                        $class->set($indexed, $value);
                    }
                }
                $class->save();
            }
        );
    }

    // Рекурсивное копирование файла
    public function copy(string $from, string $to) {
        if (file_exists($from)) {
            if (is_dir($from)) {
                @mkdir($to);
                $d = dir($from);
                while (false !== ($entry = $d->read())) {
                    if ($entry == "." || $entry == "..") continue;
                    $this->copy("$from/$entry", "$to/$entry");
                }
                $d->close();
            }
            else copy($from, $to);
        }
    }

    // рекурсивное удаление файла
    public function rm(string $path) {
        if (file_exists($path)) {
            try {
                unlink($path);
            } catch (\Exception $e) {
                $d = dir($path);
                while (false !== ($entry = $d->read())) {
                    if ($entry == "." || $entry == "..") continue;
                    $this->rm("$path/$entry");
                }
                $d->close();
                rmdir($path);
            }
        }
    }
}

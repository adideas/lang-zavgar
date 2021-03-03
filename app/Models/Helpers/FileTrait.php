<?php

namespace App\Models\Helpers;

use App\Models\File;
use App\Models\FileAndChild;
use App\Models\Key;
use App\Models\KeyAndChild;
use App\Models\Language;
use App\Models\Translate;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    // конвертация в абсолютный путь
    public function convertPath(string $path, Language $language): string
    {
        $path = implode(DIRECTORY_SEPARATOR, explode('/', $path));

        $path = str_replace(
            [DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, '${language}'],
            [DIRECTORY_SEPARATOR, $language->name],
            storage_path($path)
        );

        return $path;
    }

    // проверка наличия файла и если его нет он будет создан
    public function checkFile(File $file, Language $language, $return = true): string
    {
        if (!$language->publish) {
            return '';
        }
        $path = $this->convertPath($file->path, $language);

        if (storage_path() != $path) {
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
    public function reIndexedChildrenKey(Key $key)
    {
        function renKeys($key, $parent)
        {
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
    public function rePathChildrenFile(File $file, $parent_path = null)
    {
        if (!$parent_path) {
            $parent_path = $file->parent_file ? $file->parent_file->path : '';
        }
        $this_file_path = $parent_path . $file->name;
        $this_file_path = str_replace(['///', '//'], ['/', '/'], $this_file_path);
        if ($file->is_file) {
            $file->path = $this_file_path . '.' . strtolower($file->type->name);
        } else {
            $this_file_path = $parent_path . '/' . $file->name;
            $this_file_path = str_replace(['///', '//'], ['/', '/'], $this_file_path);
            $file->path     = $this_file_path;
        }
        $file->save();

        $children = $file->children;
        if (count($children) > 0) {
            foreach ($children as $_key => $file) {
                $this->rePathChildrenFile($file, str_replace(['///', '//'], ['/', '/'], '/' . $this_file_path . '/'));
            }
        }
    }

    // выгрузка конкретного файла
    public function exportFile(File $file)
    {
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
    public function copy(string $from, string $to)
    {
        if (file_exists($from)) {
            if (is_dir($from)) {
                @mkdir($to);
                $d = dir($from);
                while (false !== ($entry = $d->read())) {
                    if ($entry == "." || $entry == "..") {
                        continue;
                    }
                    $this->copy("$from".DIRECTORY_SEPARATOR."$entry", "$to".DIRECTORY_SEPARATOR."$entry");
                }
                $d->close();
            } else {
                copy($from, $to);
            }
        }
    }

    // рекурсивное удаление файла
    public function rm(string $path)
    {
        if (file_exists($path)) {
            try {
                unlink($path);
            } catch (\Exception $e) {
                $d = dir($path);
                while (false !== ($entry = $d->read())) {
                    if ($entry == "." || $entry == "..") {
                        continue;
                    }
                    $this->rm("$path/$entry");
                }
                $d->close();
                rmdir($path);
            }
        }
    }

    public function recursive_copy_file_keys($file, $new_file = null, $new_key = null)
    {
        $return = null;

        if (get_class($file) == FileAndChild::class) {
            // создать файл
            if (!$new_file) {
                $return = true;
            }

            $new_file = File::create(
                [
                    'name'        => $file->name,
                    'description' => $file->description,
                    'parent'      => $new_file ? $new_file->id : $file->parent,
                    'is_file'     => $file->is_file,
                    'file_type'   => $file->file_type,
                    'path'        => $file->parent_file->path . '/' . $file->name . ($file->is_file ? '.'.strtolower($file->type->name) : ''),
                ]
            );
        }
        if (get_class($file) == KeyAndChild::class) {
            // создать ключи
            $new_key = Key::create(
                [
                    'name'        => $file->name,
                    'description' => $file->description,
                    'parent'      => $new_key ? $new_key->id : null,
                    'file_id'     => $new_file ? $new_file->id : $file->id,
                    'indexed'     => $new_key ? [...$new_key->indexed, $file->name] : [$file->name],
                ]
            );
            if ($file->translate) {
                $translate            = $file->translate->toArray();
                $translate['key_id']  = $new_key->id;
                $translate['file_id'] = $new_file->id;
                $translate['user_id'] = auth()->user()->id;
                unset($translate['created_at']);
                unset($translate['updated_at']);
                unset($translate['deleted_at']);
                unset($translate['id']);
                // создаем перевод
                Translate::updateOrCreate(['key_id' => $translate['key_id']], $translate);
            }
        }
        if ($file->keys && count($file->keys)) {
            foreach ($file->keys as $_ => $key) {
                $this->recursive_copy_file_keys($key, $new_file, $new_key);
            }
        }
        if ($file->files && count($file->files)) {
            foreach ($file->files as $_ => $file) {
                $this->recursive_copy_file_keys($file, $new_file);
            }
        }
        if ($return) {
            return $new_file;
        }
    }
}

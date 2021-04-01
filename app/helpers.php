<?php
if (!function_exists('recursive_get_id')) {
    function recursive_get_id($files)
    {
        $files_id = [];
        if (isset($files->id)) {
            $files_id = [...$files_id, $files->id];
        }
        if (isset($files->files)) {
            foreach ($files->files as $_ => $files) {
                $files_id = [...recursive_get_id($files), ...$files_id];
            }
        }

        return $files_id;
    }
}

if (!function_exists('array_slice_key')) {
    /**
     * @param array $array Исходный ассоциативный массив
     * @param array $keys  Ключи которые нужно оставить
     * @return array Новый массив со срезанными ключами
     */
    function array_slice_key(array $array, ...$keys) : array
    {
        if (count($keys) == 1 && isset($keys[0]) && count($keys[0]) > 0) {
            $keys = $keys[0];
        }
        $return = [];
        foreach (array_keys($array) as $_ => $key) {
            if (in_array($key, $keys)) {
                $return[$key] = $array[$key];
            }
        }

        return $return;
    }
}

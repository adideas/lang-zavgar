<?php
if(!function_exists('recursive_get_id')) {
    function recursive_get_id($files) {
        $files_id = [];
        if(isset($files->id)) {
            $files_id = [...$files_id, $files->id];
        }
        if (isset($files->files)) {
            foreach ($files->files as $_ => $files) {
                $files_id = [...recursive_get_id($files),...$files_id];
            }
        }
        return $files_id;
    }
}

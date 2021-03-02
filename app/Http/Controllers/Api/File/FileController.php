<?php

namespace App\Http\Controllers\Api\File;

use App\Http\Controllers\Controller;
use App\Http\Filters\Filter\FileFilter;
use App\Http\Requests\Api\File\FileRequestStore;
use App\Http\Requests\Api\File\FileRequestUpdate;
use App\Models\File;
use App\Models\FileAndChild;
use App\Models\Helpers\FileTrait;
use App\Models\Key;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    use FileTrait;

    public function index(Request $request, FileFilter $fileFilter)
    {
        return FileAndChild::filter($fileFilter)->whereNull('parent')->get();
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $return = null;
        if ($request->input('method') == 'property') {
            DB::transaction(
                function () use ($id, $request, &$return) {
                    $return = $this->property(File::withTrashed()->where('id', $id)->first(), $request);
                }
            );
        }
        if ($request->input('method') == 'propertyKey') {
            DB::transaction(
                function () use ($id, $request, &$return) {
                    $return = $this->propertyKey(Key::withTrashed()->where('id', $id)->first(), $request);
                }
            );
        }

        return $return;
    }

    public function destroy(Request $request, $id)
    {
        if ($request->input('method') == 'deleteFile') {
            DB::transaction(
                function () use ($id, $request, &$return) {
                    $this->deleteFile(File::withTrashed()->where('id', $id)->first(), $request);
                }
            );
        }
        if ($request->input('method') == 'deleteKey') {
            DB::transaction(
                function () use ($id, $request, &$return) {
                    $this->deleteKey(Key::withTrashed()->where('id', $id)->first(), $request);
                }
            );
        }
    }

    public function deleteKey(Key $key, Request $request)
    {
        $key->delete();
        $this->exportFile($key->file);
    }

    public function deleteFile(File $file, Request $request)
    {
        $paths = Language::get()
            ->transform(fn($x) => [$this->convertPath($file->path, $x)])
            ->collapse()
            ->unique();

        foreach ($paths as $key => $path) {
            $this->copy($path, storage_path('backup'  . DIRECTORY_SEPARATOR . $file->name . '-' . strtotime(now())));
            $this->rm($path);
        }

        $file->delete();
    }

    public function propertyKey(Key $key, Request $request)
    {
        $key->name        = $request->input('next.name');
        $key->description = $request->input('next.description');
        $key->indexed     = [...array_slice($key->indexed, 0, count($key->indexed) - 1), $key->name];
        $key->save();
        $this->reIndexedChildrenKey($key);
        $this->exportFile($key->file);

        return $key;
    }

    public function property(File $file, Request $request)
    {
        $old_paths = Language::get()
            ->transform(fn($x) => [$this->convertPath($file->path, $x)])
            ->collapse()
            ->unique();

        $file->name        = $request->input('next.name');
        $file->description = $request->input('next.description');
        $file->save();
        $this->rePathChildrenFile($file);

        $new_paths = Language::get()
            ->transform(fn($x) => [$this->convertPath($file->path, $x)])
            ->collapse()
            ->unique();

        foreach ($old_paths as $key => $old_path) {
            $this->copy($old_path, storage_path('backup' . DIRECTORY_SEPARATOR . $file->name . '-' . strtotime(now())));
            $this->copy($old_path, $new_paths[$key]);
            $this->rm($old_path);
        }

        return $file;
    }
}

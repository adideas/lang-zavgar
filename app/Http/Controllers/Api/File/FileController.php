<?php

namespace App\Http\Controllers\Api\File;

use App\Http\Controllers\Controller;
use App\Http\Filters\Filter\FileFilter;
use App\Jobs\GitJob;
use App\Models\File;
use App\Models\FileAndChild;
use App\Models\Helpers\FileTrait;
use App\Models\Key;
use App\Models\KeyAndChild;
use App\Models\Language;
use App\Models\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        set_time_limit(400);
    }

    /*public function __construct()
    {
        $this->authorizeResource(File::class);
    }*/

    public function index(Request $request, FileFilter $fileFilter)
    {
        $this->authorize('viewAny', File::class);
        return FileAndChild::filter($fileFilter)->whereNull('parent')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('create', File::class);
        $return = null;
        if ($request->input('method') == 'makeFileDirectory') {
            DB::transaction(
                function () use ($request, &$return) {
                    $return = $this->makeFileDirectory($request);
                }
            );
        }
        if ($request->input('method') == 'makeKey') {
            DB::transaction(
                function () use ($request, &$return) {
                    $return = $this->makeKey($request);
                }
            );
        }

        GitJob::dispatch('gitDevelopPush', auth()->user()->name . ' ' . auth()->user()->email . ' (Создание) #' . auth()->user()->id)->delay(now()->addSecond(1));

        return $return;
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', File::class);
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
        if ($request->input('method') == 'copyMove') {
            DB::transaction(
                function () use ($id, $request, &$return) {
                    $return = $this->copyMove($id, $request);
                }
            );
        }

        GitJob::dispatch('gitDevelopPush', auth()->user()->name . ' ' . auth()->user()->email . ' (Обновление) #' . auth()->user()->id)->delay(now()->addSecond(1));

        return $return;
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', File::class);
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
        GitJob::dispatch('gitDevelopPush', auth()->user()->name . ' ' . auth()->user()->email . ' (Удаление) #' . auth()->user()->id)->delay(now()->addSecond(1));
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
            $this->copy($path, storage_path('backup' . DIRECTORY_SEPARATOR . $file->name . '-' . strtotime(now())));
            $this->rm($path);
        }

        $file->delete();
    }

    public function propertyKey(Key $key, Request $request)
    {
        $key->name        = $request->input('next.name');
        $key->description = $request->input('next.description', $request->input('next.name'));
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
        $file->description = $request->input('next.description', $request->input('next.name'));
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

    public function makeFileDirectory(Request $request)
    {
        $file = File::create(
            [
                'name'        => $request->input('data.name'),
                'description' => $request->input('data.description', $request->input('data.name')),
                'is_file'     => $request->input('data.is_file'),
                'parent'      => $request->input('parent'),
                'file_type'   => $request->input('data.is_file') == 0 ? null : $request->input('data.file_type'),
            ]
        );

        $this->rePathChildrenFile($file->parent_file);

        $file = FileAndChild::find($file->id);

        $old_path = '';
        Language::each(
            function (Language $language) use ($file, &$old_path) {
                $path = $this->convertPath($file->path, $language);
                if ($old_path != $path) {
                    $old_path = $path;
                    if ($file->is_file == 1) {
                        file_put_contents($path, '');
                    } else {
                        mkdir($path);
                    }
                }
            }
        );

        return $file;
    }

    public function makeKey(Request $request)
    {
        if ($request->input('data.translate') === null) {
            Cache::put('file_controller_create_key_translate_' . auth()->user()->id, 'no_create_translate', 10);
        }

        $key = Key::create(
            [
                'name'        => $request->input('data.name'),
                'description' => $request->input('data.description', $request->input('data.name')),
                'file_id'     => $request->input('file_id'),
                'parent'      => $request->input('parent'),
                'indexed'     => [$request->input('data.name')],
            ]
        );

        if ($key->parent_key) {
            $this->reIndexedChildrenKey($key->parent_key);
        }

        $this->exportFile($key->file);

        return KeyAndChild::find($key->id);
    }

    public function copyMove($id, Request $request)
    {
        $move   = $request->input('move');
        $entity = null;
        $parent = null;
        $method = $request->input('entity_type') . $request->input('parent_type');

        if ($request->input('entity_type') == 'File') {
            $entity = File::find($id);
        } elseif ($request->input('entity_type') == 'Key') {
            $entity = Key::find($id);
        }

        if ($request->input('parent_type') == 'File') {
            $parent = File::find($request->input('parent'));
        } elseif ($request->input('parent_type') == 'Key') {
            $parent = Key::find($request->input('parent'));
        }

        if ($method == 'FileFile') {
            return $this->copyMoveFileFile($entity, $parent, $move);
        } elseif ($method == 'KeyFile') {
            return $this->copyMoveKeyFile($parent, $entity, $move);
        } elseif ($method == 'KeyKey') {
            return $this->copyMoveKeyKey($entity, $parent, $move);
        } else {
            throw new \Exception('Method not allowed');
        }
    }

    public function copyMoveFileFile($file, $parent, $move)
    {
        $old_paths = Language::get()
            ->transform(fn($x) => [$this->convertPath($file->path, $x)])
            ->collapse()
            ->unique();

        if ($move) {
            $file->parent = $parent->id;
            $file->save();
            $file->path =
                $file->parent_file->path . '/' . $file->name . ($file->is_file ? '.' . strtolower($file->type->name)
                    : '');
            $file->save();
        } else {
            $file       = FileAndChild::find($file->id);
            $file->name = \request()->input('entity_new_name', $file->name);
            $file       = $this->recursive_copy_file_keys($file);
        }

        $new_paths = Language::get()
            ->transform(fn($x) => [$this->convertPath($file->path ?: '', $x)])
            ->collapse()
            ->unique();

        foreach ($old_paths as $key => $old_path) {
            $this->copy($old_path, storage_path('backup' . DIRECTORY_SEPARATOR . $file->name . '-' . strtotime(now())));
            $this->copy($old_path, $new_paths[$key]);
            if ($move) {
                $this->rm($old_path);
            }
        }
    }

    public function copyMoveKeyFile($file, $key, $move)
    {
        $old_id_file = $key->file->id;
        $new_id_file = $file->id;
        $key->file_id = $new_id_file;
        $key->indexed = [$key->name];
        $key->parent = null;
        $key->save();
        Translate::where(['file_id' => $old_id_file, 'key_id' => $key->id])->update(['file_id' => $new_id_file]);

        if($key->parent_key) {
            $this->reIndexedChildrenKey($key->parent_key);
        }
        $this->exportFile(File::find($old_id_file));
        $this->exportFile(File::find($new_id_file));
    }

    public function copyMoveKeyKey($key, $parent, $move)
    {
        $old_id_file = $key->file->id;
        $new_id_file = $parent->file->id;
        $key->parent = $parent->id;
        $key->indexed = [$key->name];
        $key->save();
        Translate::where(['file_id' => $old_id_file, 'key_id' => $key->id])->update(['file_id' => $new_id_file]);

        if($key->parent_key) {
            $this->reIndexedChildrenKey($key->parent_key);
        }

        $this->exportFile(File::find($old_id_file));
        $this->exportFile(File::find($new_id_file));
    }
}

<?php

namespace App\Http\Controllers\Api\File;

use App\Http\Controllers\Controller;
use App\Http\Filters\Filter\FileFilter;
use App\Http\Requests\Api\File\FileRequestStore;
use App\Http\Requests\Api\File\FileRequestUpdate;
use App\Models\File;
use App\Models\FileAndChild;
use App\Models\Helpers\FileTrait;
use Illuminate\Http\Request;

class FileController extends Controller
{
    use FileTrait;

    public function index(Request $request, FileFilter $fileFilter)
    {
        if ($request->input('only') == 'delete') {
            return File::onlyTrashed()->get();;
        }

        //  withoutGlobalScopes
        return FileAndChild::filter($fileFilter)->whereNull('parent')->get();
    }

    public function store(FileRequestStore $request)
    {
        $file = File::create(
            [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'is_file' => $request->input('is_file', 0),
                'file_type' => $request->input('is_file', 0) > 0 ? $request->input('file_type') : null,
                'parent' => $request->input('parent')
            ]
        );

        $this->rePathChildrenFile($file);
        $this->syncFolder();
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function update(FileRequestUpdate $request, $id)
    {
        $file = File::withTrashed()->where('id', $id)->first();

        if ($request->input('path_parents') && count($request->input('path_parents'))) {
            $file->parent = $request->input('path_parents')[count($request->input('path_parents')) - 1];
            $file->save();
            $this->rePathChildrenFile($file);
            $this->syncFolder();
        } else {
            $file->name        = $request->input('name');
            $file->description = $request->input('description');
            if ($request->input('is_file')) {
                $file->is_file = $request->input('is_file', 0);
            }
            if ($request->input('file_type')) {
                $file->file_type = $request->input('file_type', null);
            }
            $file->save();
            $this->rePathChildrenFile($file);
            $this->syncFolder();
        }
    }

    public function destroy($id)
    {
        function delete_func($files)
        {
            foreach ($files as $key => $file) {
                $children = $file->children;
                if ($children) {
                    delete_func($children);
                }
                $file->delete();
            }
        }

        function restore_func($file)
        {
            if ($file) {
                if ($file->deleted_at) {
                    $file->restore();
                    restore_func($file->parent_file);
                }
            }
        }

        $file = File::withTrashed()->where('id', $id)->first();

        if ($file->deleted_at) {
            restore_func($file->parent_file);
            $file->restore();
        } else {
            delete_func($file->children);
            $file->delete();
        }

        $this->syncFolder();
    }
}

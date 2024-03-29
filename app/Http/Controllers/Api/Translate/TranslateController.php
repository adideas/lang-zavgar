<?php

namespace App\Http\Controllers\Api\Translate;

use App\Http\Controllers\Controller;
use App\Http\Filters\Filter\TranslateFilter;
use App\Http\Requests\Api\Translate\TranslateRequest;
use App\Http\Resources\Api\Dashboard\DashboardResource;
use App\Http\Resources\Api\Translate\TranslateResource;
use App\Jobs\GitJob;
use App\Models\File;
use App\Models\FileAndChildOnlyId;
use App\Models\Helpers\FileTrait;
use App\Models\Key;
use App\Models\KeyAndChildOnlyId;
use App\Models\Search;
use App\Models\Translate;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->authorizeResource(Translate::class);
    }

    public function index(Request $request, TranslateFilter $translateFilter)
    {
        // создаем запрос
        $translate = Translate::filter($translateFilter);

        // доп фильтр для поиска
        $filter = json_decode($request->input('filter'), true);
        if($filter['model_id']) {
            $model = Search::find($filter['model_id']);
            if ($model) {
                if($model->entity === Translate::class) {
                    $translate = Translate::where('id', $model->model->id);
                } elseif ($model->entity === File::class) {
                    $files = FileAndChildOnlyId::find($model->model->id);
                    $translate = Translate::whereIn('file_id', recursive_get_id($files));
                } elseif ($model->entity === Key::class) {
                    $keys = KeyAndChildOnlyId::find($model->model->id);
                    $translate = Translate::whereIn('key_id', recursive_get_id($keys));
                }
            }
        } else {
            $translate = Translate::filter($translateFilter);
        }

        // вернем коллекцию
        return TranslateResource::collection($translate->with('key:id,name,description')->paginate($request->to ?: 5));
    }

    public function update(TranslateRequest $request, Translate $translate)
    {
        $value = $request->input('value', null);

        $value = $value && strlen($value) ? $value : null;

        $translate->update(
            [
                '0' . $request->input('language_id') => $value,
                'user_id' => auth()->user()->id,
            ]
        );

        $this->exportFile(File::find($translate->file_id));

        //GitJob::dispatch('gitDevelopPush', auth()->user()->name . ' ' . auth()->user()->email . ' (Перевод) #' . auth()->user()->id)->delay(now()->addSecond(1));
    }

    public function store(Request $request) {
        if ($request->input('type', '') == 'master') {
            $this->authorize('create', Translate::class);
            GitJob::dispatch('gitMasterPush', $request->input('repo', ''))->delay(now()->addSecond(1));
        }
        if ($request->input('type', '') == 'develop') {
            File::where('is_file', 1)->each(
                function (File $file) {
                    $this->exportFile($file);
                }
            );

            GitJob::dispatch('gitDevelopPush', auth()->user()->name . ' ' . auth()->user()->email . ' (Экспорт) #' . auth()->user()->id)->delay(now()->addSecond(1));
        }
    }

    public function dashboard() {
        return DashboardResource::collection(collect(''))[0];
    }
}

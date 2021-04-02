<?php

namespace App\Console\Commands;

use App\classes\DiffText\DiffText;
use App\classes\GitInject\GitInject;
use App\Models\Access;
use App\Models\File;
use App\Models\GitCommand;
use App\Models\Helpers\FileTrait;
use App\Models\HistoryTranslate;
use App\Models\Key;
use App\Models\Language;
use App\Models\Search;
use App\Models\Translate;
use App\User;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Dump extends Command
{
    use FileTrait;

    protected $signature   = 'dd';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       //
        dd(HistoryTranslate::distinct('date')->get());
    }
}


// dd(Key::whereJsonContains('indexed', $indexed_[0])->first());
/*DB::table('ltm_translations')
    ->where('group', $data->group)
    ->where('key', $data->key)
    ->get()->each(
        function($translate) use () {
            $language = Language::where('name', $translate->locale)->first();

            Translate::where('key_id', )
                       }
    );*/

// http://bit-booster.com/graph.html
// https://gitgraphjs.com/#0
// http://bsara.github.io/git-grapher/
// https://qastack.ru/programming/1057564/pretty-git-branch-graphs

// TODO translates_keys_key_id_foreign

/*DB::table('ltm_translations')
    ->orderByDesc('key')
    ->selectRaw('DISTINCT `key`, `group`')
    ->where('group', 'LIKE', 'mobile/%')
    // ->where('group', 'LIKE', 'front/%')
    ->get()
    ->each(
        function ($data) {
            dump($data->key);
            $indexed = explode('|', $data->key);
            $file_id = 96;

            if (count($indexed) == 1) {
                $parent = Key::where('file_id', $file_id)
                    ->where('indexed', 'LIKE', '%' . implode('%', [$indexed[0]]) . '%')
                    ->where('name', $indexed[0])
                    ->whereNull('parent')->first();

                if (!$parent) {
                    dump('create root key');
                    $parent = Key::create(
                        [
                            'name'        => $indexed[0],
                            'description' => $indexed[0],
                            'indexed'     => $indexed,
                            'parent'      => null,
                            'file_id'     => $file_id,
                        ]
                    );
                }
            }


            if (count($indexed) == 2) {
                $parent = Key::where('file_id', $file_id)
                    ->where('indexed', 'LIKE', '%' . implode('%', [$indexed[0]]) . '%')
                    ->where('name', $indexed[0])
                    ->whereNull('parent')
                    ->first();

                if (!$parent) {
                    dump('create root key');
                    $parent = Key::create(
                        [
                            'name'        => $indexed[0],
                            'description' => $indexed[0],
                            'indexed'     => [$indexed[0]],
                            'parent'      => null,
                            'file_id'     => $file_id,
                        ]
                    );
                }

                $key = Key::where('file_id', $file_id)
                    ->where('indexed', 'LIKE', '%' . implode('%', $indexed) . '%')
                    ->where('name', $indexed[1])
                    ->where('parent', $parent->id)
                    ->first();

                if (!$key) {
                    dump('create key');
                    Key::create(
                        [
                            'name'        => $indexed[1],
                            'description' => $indexed[1],
                            'indexed'     => $indexed,
                            'parent'      => $parent->id,
                            'file_id'     => $file_id,
                        ]
                    );
                }
            }
        }
    );*/

/* TODO SQL
Поиск русских букв там где должны быть англ
SELECT `02`
FROM `translates`
WHERE regexp_like(UPPER(`02`),'[А-Я]')

UPDATE `translates` SET `02`=NULL WHERE regexp_like(UPPER(`02`),'[А-Я]');*/

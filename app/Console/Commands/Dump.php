<?php

namespace App\Console\Commands;

use App\classes\GitInject\GitInject;
use App\Models\Access;
use App\Models\File;
use App\Models\GitCommand;
use App\Models\Helpers\FileTrait;
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
    }
}


// http://bit-booster.com/graph.html
// https://gitgraphjs.com/#0
// http://bsara.github.io/git-grapher/
// https://qastack.ru/programming/1057564/pretty-git-branch-graphs


// TODO translates_keys_key_id_foreign


/* TODO DB::table('ltm_translations')
    // ->where('group', 'LIKE', 'mobile/%')
    // ->where('group', 'LIKE', 'front/%')
    ->get()->each(
        function ($data) {
            $lang_id = Language::where('name', $data->locale)->first()->id;

            preg_match('/front\//', $data->group, $front);
            preg_match('/mobile\//', $data->group, $mobile);

            $file = null;

            if (count($front)) {
                // front
                $file = File::where('path', 'LIKE', "%" . implode('%', explode('/', $data->group)) . "%")->whereNotNull('file_type')->first();
            }
            if (count($mobile)) {
                // mobile
                $file = File::find(96);
            }
            if (count($front) == 0 && count($mobile) == 0) {
                // back
                $file = File::where('path', 'LIKE', "%back%$data->group%")->whereNotNull('file_type')->first();
            }

            $keys = explode('|', $data->key);
            $key  = null;

            $fun_create = function ($_d, $d_2) {
                $id = null;
                Key::where(
                    [
                        'name'        => $_d['name'],
                        'description' => $_d['description'],
                    ]
                )->each(
                    function (Key $key) use ($_d, &$id) {
                        if ($key->indexed == $_d['indexed']) {
                            $id = $key->id;
                        }
                    }
                );

                if ($id) {
                    return Key::find($id);
                } else {
                    return Key::create($d_2);
                }
            };

            if (count($keys) > 0) {
                $_d = [
                    'name'        => $keys[0],
                    'description' => $keys[0],
                    'file_id'     => $file->id,
                    'indexed'     => [$keys[0]],
                ];

                $key = $fun_create(
                    $_d,
                    $_d
                );

                if (count($keys) == 1) {
                    Translate::where('key_id', $key->id)->update(
                        [
                            '0' . $lang_id => $data->value,
                        ]
                    );
                }

                if (count($keys) > 1) {
                    $_d = [
                        'name'        => $keys[1],
                        'description' => $keys[1],
                        'file_id'     => $file->id,
                        'indexed'     => [$keys[0], $keys[1]],
                    ];

                    $key = $fun_create(
                        $_d,
                        array_replace(
                            $_d,
                            [
                                'parent' => $key->id,
                            ]
                        )
                    );

                    if (count($keys) == 2) {
                        Translate::where('key_id', $key->id)->update(
                            [
                                '0' . $lang_id => $data->value,
                            ]
                        );
                    }

                    if (count($keys) > 2) {
                        $_d = [
                            'name'        => $keys[2],
                            'description' => $keys[2],
                            'file_id'     => $file->id,
                            'indexed'     => [$keys[0], $keys[1], $keys[2]],
                        ];

                        $key = $fun_create(
                            $_d,
                            array_replace(
                                $_d,
                                [
                                    'parent' => $key->id,
                                ]
                            )
                        );

                        if (count($keys) == 3) {
                            Translate::where('key_id', $key->id)->update(
                                [
                                    '0' . $lang_id => $data->value,
                                ]
                            );
                        }

                        if (count($keys) > 3) {
                            $_d = [
                                'name'        => $keys[3],
                                'description' => $keys[3],
                                'file_id'     => $file->id,
                                'indexed'     => [$keys[0], $keys[1], $keys[2], $keys[3]],
                            ];

                            $key = $fun_create(
                                $_d,
                                array_replace(
                                    $_d,
                                    [
                                        'parent' => $key->id,
                                    ]
                                )
                            );

                            if (count($keys) == 4) {
                                Translate::where('key_id', $key->id)->update(
                                    [
                                        '0' . $lang_id => $data->value,
                                    ]
                                );
                            }

                            if (count($keys) > 4) {
                                $_d = [
                                    'name'        => $keys[4],
                                    'description' => $keys[4],
                                    'file_id'     => $file->id,
                                    'indexed'     => [$keys[0], $keys[1], $keys[2], $keys[3], $keys[4]],
                                ];

                                $key = $fun_create(
                                    $_d,
                                    array_replace(
                                        $_d,
                                        [
                                            'parent' => $key->id,
                                        ]
                                    )
                                );

                                if (count($keys) == 5) {
                                    Translate::where('key_id', $key->id)->update(
                                        [
                                            '0' . $lang_id => $data->value,
                                        ]
                                    );
                                }

                                if (count($keys) > 5) {
                                    $_d = [
                                        'name'        => $keys[5],
                                        'description' => $keys[5],
                                        'file_id'     => $file->id,
                                        'indexed'     => [
                                            $keys[0],
                                            $keys[1],
                                            $keys[2],
                                            $keys[3],
                                            $keys[4],
                                            $keys[5],
                                        ],
                                    ];

                                    $key = $fun_create(
                                        $_d,
                                        array_replace(
                                            $_d,
                                            [
                                                'parent' => $key->id,
                                            ]
                                        )
                                    );

                                    if (count($keys) == 6) {
                                        Translate::where('key_id', $key->id)->update(
                                            [
                                                '0' . $lang_id => $data->value,
                                            ]
                                        );
                                    }

                                    if (count($keys) > 6) {
                                        dd('gusdfgasdfasdfgjdfsjasdfjjsdfajfkasdjkf');
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // dump($keys,$key_name);
        }
    );*/






/* TODO SQL
Поиск русских букв там где должны быть англ
SELECT `02`
FROM `translates`
WHERE regexp_like(UPPER(`02`),'[А-Я]')

UPDATE `translates` SET `02`=NULL WHERE regexp_like(UPPER(`02`),'[А-Я]');*/

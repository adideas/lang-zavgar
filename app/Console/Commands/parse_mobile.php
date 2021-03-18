<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Key;
use App\Models\Translate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class parse_mobile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse_mobile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('ltm_translations')
            ->orderByDesc('key')
            ->selectRaw('DISTINCT `key`, `group`, `locale`')
            ->where('group', 'LIKE', 'mobile/%')
            ->where('locale', 'ru')
            ->get()
            ->each(
                function ($data) {
                    $this->warn('name key: '.$data->key.' '.$data->group);
                    $indexed = explode('|', $data->key);
                    $file_id = 96;

                    if (count($indexed) == 1) {
                        $parent = Key::where('file_id', $file_id)
                            ->where('indexed', 'LIKE', '%' . implode('%', [$indexed[0]]) . '%')
                            ->where('name', $indexed[0])
                            ->whereNull('parent')->first();

                        if (!$parent) {
                            $parent = Key::create(
                                [
                                    'name'        => $indexed[0],
                                    'description' => $indexed[0],
                                    'indexed'     => $indexed,
                                    'parent'      => null,
                                    'file_id'     => $file_id,
                                ]
                            );
                            $this->error('create root key: '. $parent->name . ' ');
                        }

                        Translate::where('key_id', $parent->id)
                            ->update(
                                [
                                    '01' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'ru')
                                            ->first()->value ?? null,
                                    '02' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'en')
                                            ->first()->value ?? null,
                                    '03' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'es')
                                            ->first()->value ?? null,
                                    '04' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'hy')
                                            ->first()->value ?? null,
                                ]
                            );
                    }

                    if (count($indexed) == 2) {
                        $parent = Key::where('file_id', $file_id)
                            ->where('indexed', 'LIKE', '%' . implode('%', [$indexed[0]]) . '%')
                            ->where('name', $indexed[0])
                            ->whereNull('parent')
                            ->first();

                        if (!$parent) {
                            $parent = Key::create(
                                [
                                    'name'        => $indexed[0],
                                    'description' => $indexed[0],
                                    'indexed'     => [$indexed[0]],
                                    'parent'      => null,
                                    'file_id'     => $file_id,
                                ]
                            );
                            $this->error('create root key: '. $parent->name . ' ');
                        }

                        $key = Key::where('file_id', $file_id)
                            ->where('indexed', 'LIKE', '%' . implode('%', $indexed) . '%')
                            ->where('name', $indexed[1])
                            ->where('parent', $parent->id)
                            ->first();

                        if (!$key) {
                            $key = Key::create(
                                [
                                    'name'        => $indexed[1],
                                    'description' => $indexed[1],
                                    'indexed'     => $indexed,
                                    'parent'      => $parent->id,
                                    'file_id'     => $file_id,
                                ]
                            );
                            $this->info('create children key: '. $parent->name . ' ');
                        }

                        Translate::where('key_id', $key->id)
                            ->update(
                                [
                                    '01' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'ru')
                                            ->first()->value ?? null,
                                    '02' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'en')
                                            ->first()->value ?? null,
                                    '03' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'es')
                                            ->first()->value ?? null,
                                    '04' => DB::table('ltm_translations')
                                            ->where('group', $data->group)
                                            ->where('key', $data->key)
                                            ->where('locale', 'hy')
                                            ->first()->value ?? null,
                                ]
                            );
                    }
                }
            );

        $last_key_id = Key::orderByDesc('id')->first()->id;

        DB::table('ltm_translations')
            ->orderByDesc('key')
            ->selectRaw('DISTINCT `key`, `group`, `locale`')
            ->where('group', 'LIKE', 'front/%')
            ->where('locale', 'ru')
            ->get()
            ->each(
                function ($data) use ($last_key_id) {
                    $file    = File::where('path', 'LIKE', str_replace('front/', '%', $data->group))->first();
                    $indexed = explode('|', $data->key);
                    $this->warn('name key: '.$data->key.' '.$data->group);
                    $file_id = $file->id;

                    $parent_old = new \stdClass(['id' => null]);

                    foreach ($indexed as $index => $_) {
                        $parent = Key::where('file_id', $file_id)
                            ->where('indexed', 'LIKE', '%' . implode('%', array_slice($indexed, 0, $index + 1)) . '%')
                            ->where('name', $indexed[$index])
                            ->where('id', '>',$last_key_id)
                            ->when($index == 0, fn($x) => $x->whereNull('parent'))
                            ->when($index > 0, fn($x) => $x->where('parent', $parent_old->id))
                            ->first();

                        if (!$parent) {
                            $parent = Key::create(
                                [
                                    'name'        => $indexed[$index],
                                    'description' => $indexed[$index],
                                    'indexed'     => array_slice($indexed, 0, $index + 1),
                                    'parent'      => $index == 0 ? null : $parent_old->id,
                                    'file_id'     => $file_id,
                                ]
                            );
                            if($index == 0) {
                                $this->error('create '. ($index == 0 ? 'root' : 'children') .' key: '. $parent->name . ' ' . $file->path);
                            } else {
                                $this->info('create '. ($index == 0 ? 'root' : 'children') .' key: '. $parent->name . ' ' . $file->path);
                            }
                        }

                        if (count($indexed) == $index + 1) {
                            $parent_old = new \stdClass(['id' => null]);
                            Translate::where('key_id', $parent->id)
                                ->update(
                                    [
                                        '01' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'ru')
                                                ->first()->value ?? null,
                                        '02' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'en')
                                                ->first()->value ?? null,
                                        '03' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'es')
                                                ->first()->value ?? null,
                                        '04' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'hy')
                                                ->first()->value ?? null,
                                    ]
                                );
                        } else {
                            $parent_old = $parent;
                            if(Translate::where('key_id', $parent->id)->first()) {
                                Translate::where('key_id', $parent->id)
                                    ->update(
                                        [
                                            '01' => DB::table('ltm_translations')
                                                    ->where('group', $data->group)
                                                    ->where('key', $data->key)
                                                    ->where('locale', 'ru')
                                                    ->first()->value ?? null,
                                            '02' => DB::table('ltm_translations')
                                                    ->where('group', $data->group)
                                                    ->where('key', $data->key)
                                                    ->where('locale', 'en')
                                                    ->first()->value ?? null,
                                            '03' => DB::table('ltm_translations')
                                                    ->where('group', $data->group)
                                                    ->where('key', $data->key)
                                                    ->where('locale', 'es')
                                                    ->first()->value ?? null,
                                            '04' => DB::table('ltm_translations')
                                                    ->where('group', $data->group)
                                                    ->where('key', $data->key)
                                                    ->where('locale', 'hy')
                                                    ->first()->value ?? null,
                                        ]
                                    );
                            }
                        }
                    }
                }
            );

        $last_key_id = Key::orderByDesc('id')->first()->id;

        DB::table('ltm_translations')
            ->orderByDesc('key')
            ->selectRaw('DISTINCT `key`, `group`, `locale`')
            ->where('group', 'NOT LIKE', 'front/%')
            ->where('group', 'NOT LIKE', 'mobile/%')
            ->where('locale', 'ru')
            ->get()
            ->each(
                function ($data) use ($last_key_id) {
                    $file    = File::where('path', 'LIKE', '%'.'back/${language}/'.$data->group.'%')->first();
                    $indexed = explode('|', $data->key);
                    $this->warn('name key: '.$data->key.' '.$data->group);
                    $file_id = $file->id;

                    $parent_old = new \stdClass(['id' => null]);

                    foreach ($indexed as $index => $_) {
                        $parent = Key::where('file_id', $file_id)
                            ->where('indexed', 'LIKE', '%' . implode('%', array_slice($indexed, 0, $index + 1)) . '%')
                            ->where('name', $indexed[$index])
                            ->where('id', '>',$last_key_id)
                            ->when($index == 0, fn($x) => $x->whereNull('parent'))
                            ->when($index > 0, fn($x) => $x->where('parent', $parent_old->id))
                            ->first();

                        if (!$parent) {
                            $parent = Key::create(
                                [
                                    'name'        => $indexed[$index],
                                    'description' => $indexed[$index],
                                    'indexed'     => array_slice($indexed, 0, $index + 1),
                                    'parent'      => $index == 0 ? null : $parent_old->id,
                                    'file_id'     => $file_id,
                                ]
                            );
                            if($index == 0) {
                                $this->error('create '. ($index == 0 ? 'root' : 'children') .' key: '. $parent->name . ' ' . $file->path);
                            } else {
                                $this->info('create '. ($index == 0 ? 'root' : 'children') .' key: '. $parent->name . ' ' . $file->path);
                            }
                        }

                        if (count($indexed) == $index + 1) {
                            $parent_old = new \stdClass(['id' => null]);
                            Translate::where('key_id', $parent->id)
                                ->update(
                                    [
                                        '01' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'ru')
                                                ->first()->value ?? null,
                                        '02' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'en')
                                                ->first()->value ?? null,
                                        '03' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'es')
                                                ->first()->value ?? null,
                                        '04' => DB::table('ltm_translations')
                                                ->where('group', $data->group)
                                                ->where('key', $data->key)
                                                ->where('locale', 'hy')
                                                ->first()->value ?? null,
                                    ]
                                );
                        } else {
                            $parent_old = $parent;
                        }
                    }
                }
            );
    }
}

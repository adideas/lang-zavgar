<?php

namespace App\Console\Commands;

use App\Models\Translate;
use App\User;
use App\UserAccess;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Dump extends Command
{
    protected $signature   = 'dd';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        dd(UserAccess::get());

        /*$coder = new ENVFileCoder('C:\OSPanel\domains\lang.zavgar.online\storage\back\ru\auth.env');

        $coder->set(['b','d','c','g','e'], 10)->set(['b','d','h','g','e'], 10)->set(['h','d','c','g','e'], 10)->save();

        dd();
        dd(FileType::get());*/

        /*Translate::create(
            [
                'key_id' => 3,
                'file_id' => 4,
                '01'     => Str::random(10),
            ]
        );*/

        $translate = Translate::first();

        dump($translate);

        $translate->update([
            '02' => Str::random(10)
                           ]);

        /*$translate = Translate::find(10);

        $translate->update([
                               '02' => Str::random(10)
                           ]);*/
        // dd(($translate->storage(1))->set(['test','d'], 'key'));
    }
}


// http://bit-booster.com/graph.html
// https://gitgraphjs.com/#0
// http://bsara.github.io/git-grapher/
// https://qastack.ru/programming/1057564/pretty-git-branch-graphs

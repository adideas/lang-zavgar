<?php

namespace App\Console\Commands;

use App\Models\Access;
use App\Models\File;
use App\Models\Helpers\FileTrait;
use App\Models\Translate;
use App\User;
use Closure;
use Illuminate\Console\Command;
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
        $access = User::find(1)->getAccess();


        dd(json_decode(json_encode($access)));
    }
}


// http://bit-booster.com/graph.html
// https://gitgraphjs.com/#0
// http://bsara.github.io/git-grapher/
// https://qastack.ru/programming/1057564/pretty-git-branch-graphs

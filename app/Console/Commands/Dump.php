<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Helpers\FileTrait;
use App\Models\Translate;
use App\User;
use App\UserAccess;
use Closure;
use Illuminate\Console\Command;
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
        $this->rePathChildrenFile(File::find(1));
    }
}


// http://bit-booster.com/graph.html
// https://gitgraphjs.com/#0
// http://bsara.github.io/git-grapher/
// https://qastack.ru/programming/1057564/pretty-git-branch-graphs

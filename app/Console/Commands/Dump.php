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
        $model = Translate::first();
        $searchable = $model->searchable;

        $string = ' ';

        foreach ($model->getAttributes() as $key => $value) {
            if(gettype($value) == 'string' && strtotime($value) < 1500000000) {
                $string .= $value . ' ';
            }
        }

        if (isset($model->searchable)) {
            foreach ($model->searchable as $relation => $params) {
                $params = explode('.', $params);
                if(count($params) == 2) {
                    $data = $model->{$params[0]}()->select(['id',...explode(',',$params[1])])->get()->toArray()[0];
                    foreach ($data as $key => $value) {
                        if(gettype($value) == 'string' && strtotime($value) < 1500000000) {
                            $string .= $value . ' ';
                        }
                    }
                }
            }
        }

        dd($string);
    }
}


// http://bit-booster.com/graph.html
// https://gitgraphjs.com/#0
// http://bsara.github.io/git-grapher/
// https://qastack.ru/programming/1057564/pretty-git-branch-graphs

<?php

namespace App\Http\Resources\Api\Dashboard;

use App\classes\GitInject\GitInject;
use App\Models\Language;
use App\Models\Translate;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray($request)
    {
        /*$data = cache()->rememberForever('git_dashboard_status', function () {

        });*/

        $repos = [];

        $gitInject = new GitInject();

        foreach ($gitInject->dir_git as $k_ => $name) {
            $git = new GitInject($name);
            $repos = array_replace($repos, [
                $name => [
                    'branch' => $git->branch,
                    'branches' => $git->branches,
                    'last_commit' => $git->getLastCommit(),
                    'status' => $git->getStatus(),
                ]
            ]);
        }

        return [
            'git' => $repos,
            'key_status' => [
                Translate::count(),
                Translate::where(function ($builder) {
                    Language::pluck('id')->map(fn($x) => '0' . $x)
                        ->each(fn($lang) => $builder->orWhereNull($lang));
                })->count()
            ]
        ];

        // return $data;
    }
}

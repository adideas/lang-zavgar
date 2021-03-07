<?php

namespace App\Http\Resources\Api\Dashboard;

use App\classes\GitInject\GitInject;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray($request)
    {
        $repos = cache()->rememberForever('git_dashboard_status', function () {
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

            return $repos;
        });


        return [
            'git' => $repos,
        ];
    }
}

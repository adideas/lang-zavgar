<?php

namespace App\Jobs;

use App\classes\GitInject\GitInject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $command;
    protected $commit;

    public function __construct($command, $commit)
    {
        $this->command = $command;
        $this->commit = $commit;
    }

    public function handle()
    {
        $git = new GitInject();
        if ($this->command == 'gitDevelopPush') {
            foreach ($git->dir_git as $key => $name) {
                try {
                    $git = new GitInject($name);
                    $git->gitDevelopPush($this->commit);
                } catch (\Exception $e) {
                    dump($e);
                }
            }
        }
        if ($this->command == 'gitMasterPush') {
            if(strlen($this->commit) > 2) {
                try {
                    $git = new GitInject($this->commit);
                    $git->gitMasterPush();
                } catch (\Exception $e) {
                    dump($e);
                }
            } else {
                foreach ($git->dir_git as $key => $name) {
                    try {
                        $git = new GitInject($name);
                        $git->gitMasterPush();
                    } catch (\Exception $e) {
                        dump($e);
                    }
                }
            }
        }
    }
}

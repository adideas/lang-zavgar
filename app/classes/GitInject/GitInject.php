<?php

namespace App\classes\GitInject;

use App\Models\File;

class GitInject
{
    private string $root_directory = '';

    private string $directory      = '';

    public array   $branches       = [];

    public string  $branch         = '';

    public bool    $remote         = false;

    public array   $dir_git        = [];

    public function __construct(string $directory = '')
    {
        // получаем главную директорию
        $root_path      = File::where('root', 1)->first();
        $root_directory = str_replace(
            DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            storage_path(str_replace('/', DIRECTORY_SEPARATOR, $root_path->path . DIRECTORY_SEPARATOR . $directory))
        );

        if ($directory == '') {
            $dir = array_values(
                array_filter(
                    scandir($root_directory),
                    fn($x) => $x != '.' && $x != '..' && is_dir($root_directory . DIRECTORY_SEPARATOR . $x)
                )
            );

            $out_dir_in_git = [];

            foreach ($dir as $_ => $name) {
                $path = $root_directory . DIRECTORY_SEPARATOR . $name;
                if (in_array('.git', scandir($path))) {
                    $out_dir_in_git[] = $name;
                }
            }

            $this->dir_git = $out_dir_in_git;
            return;
        }

        if (file_exists($root_directory)) {
            $this->root_directory = $root_directory;
            $this->directory      = $directory;
        } else {
            throw new \Exception($root_directory . "\n Нет такой папки!\n GitInject");
        }

        $this->getBranches();
    }

    private function exec(array $commands, bool $implode = true, bool $return_error = false)
    {
        $commands = ["cd $this->root_directory", ...$commands];
        try {
            exec(implode(' && ', $commands), $output, $error);
            if ($error) {
                $no_commit = strpos(implode(' ', $output), 'nothing to commit');
                if ($no_commit) {
                    //
                    return $return_error ? $error : ($implode ? implode("\n", $output) : $output);
                } else {
                    exec('whoami', $out);
                    $out = implode(' ', $out);
                    throw new \Exception(
                        implode("\n", $output) . "error code: $error ($out) " . " -> " . implode(' && ', $commands)
                    );
                }
            } else {
                return $return_error ? $error : ($implode ? implode("\n", $output) : $output);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function gitDevelopPush(string $name_commit)
    {
        cache()->delete('git_dashboard_status');

        if($this->directory == '') {
            throw new \Exception('Не указана папка');
            return;
        }
        if (!in_array('develop', $this->branches)) {
            if ($this->branch != 'master') {
                throw new \Exception('git add Только в ветке master');
            }
            $command = ["git add .", 'git commit -m "' . $name_commit . '"'];
            if ($this->remote) {
                $command[] = 'git push';
            }
            $this->exec($command);
        } else {
            if ($this->branch != 'develop') {
                throw new \Exception('git add Только в ветке develop');
            }
            $command = ["git add .", 'git commit -m "' . $name_commit . '"'];
            if ($this->remote) {
                $command[] = 'git push';
            }
            $this->exec($command);
        }
    }

    public function gitMasterPush()
    {
        if($this->directory == '') {
            throw new \Exception('Не указана папка');
            return;
        }
        if ($this->branch != 'develop') {
            throw new \Exception('git Только в ветке develop');
        }
        $output = $this->exec(['git status']);
        if (strpos($output, 'modified:')) {
            throw new \Exception('ветка develop имеет изменения');
        }
        // git merge -s recursive -Xtheirs master https://habr.com/ru/post/195674/
        $command = [
            'git checkout master',
            'git merge -s recursive -Xtheirs develop',
        ];

        if ($this->remote) {
            $command[] = 'git push';
        }
        $command[] = 'git checkout develop';

        $this->exec($command);
    }

    public function getLastCommit()
    {
        $commands = ["git log -1"];

        return $this->exec($commands, false);
    }

    public function getStatus()
    {
        $commands = ["git status -vvv"];

        return $this->exec($commands, false);
    }

    private function getBranches()
    {
        $commands       = ["git branch"];
        $output         = $this->exec($commands, false);
        $this->branches = array_map(fn($x) => str_replace(['* ', ' '], ['', ''], $x), $output);

        foreach ($output as $_ => $value) {
            if ($value[0] == '*') {
                $this->branch = str_replace(['* ', ' '], ['', ''], $value);
                break;
            }
        }

        $this->remote = $this->exec(['git remote show']) == 'origin';
    }
}

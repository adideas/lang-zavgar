<?php

namespace App\Console\Commands;

use App\Jobs\GitJob;
use App\Models\File;
use App\Models\Helpers\FileTrait;
use App\Models\HistoryTranslate;
use Illuminate\Console\Command;

class CheckLangAndPushGit extends Command
{
    use FileTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_and_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check lang and push';

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
        if (HistoryTranslate::where('created_at', '>=', now()->addMinutes(-10))->count()) {
            File::where('is_file', 1)->each(
                function (File $file) {
                    $this->exportFile($file);
                }
            );

            GitJob::dispatch('gitDevelopPush', ' (Экспорт) #')->delay(now()->addSecond(1));
        }
    }
}

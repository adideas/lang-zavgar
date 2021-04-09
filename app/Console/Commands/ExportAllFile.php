<?php

namespace App\Console\Commands;

use App\Jobs\GitJob;
use App\Models\File;
use App\Models\Helpers\FileTrait;
use Illuminate\Console\Command;

class ExportAllFile extends Command
{
    use FileTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export_all_file';

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
        File::where('is_file', 1)->each(
            function (File $file) {
                $this->exportFile($file);
            }
        );

        GitJob::dispatch('gitDevelopPush', ' (Экспорт) #')->delay(now()->addSecond(1));
    }
}

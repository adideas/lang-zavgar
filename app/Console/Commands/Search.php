<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Key;
use App\Models\Translate;
use App\Observers\SearchableObserver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Search extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rebuild search';

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
        DB::unprepared("TRUNCATE `searches`;");

        $observer = new SearchableObserver();

        Translate::each(
            function (Translate $file) use ($observer) {
                $observer->updated($file);
            }
        );

        File::each(
            function (File $file) use ($observer) {
                $observer->updated($file);
            }
        );

        Key::each(
            function (Key $file) use ($observer) {
                $observer->updated($file);
            }
        );
    }
}

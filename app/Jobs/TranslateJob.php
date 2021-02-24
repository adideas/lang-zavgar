<?php

namespace App\Jobs;

use App\Models\Translate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class TranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Translate $translate;
    protected array $change;

    public function __construct(array $change, Translate $translate)
    {
        //
        $this->change = $change;
        $this->translate = $translate;
    }

    public function handle()
    {
        $this->run_command();
    }

    public function run_command() {
        $lang = array_values(array_filter(array_keys($this->change), fn($x) => is_numeric($x)));
        if (count($lang) > 0) {
            foreach ($lang as $_ => $language_id) {
                $language_id = intval($language_id);
                $this->translate->storage($language_id)->set($this->translate->key->indexed, $this->change['0'.$language_id])->save();
            }
            //
        }
    }
}

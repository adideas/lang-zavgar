<?php

namespace App\Observers;

use App\Jobs\TranslateJob;

class TranslateObserver
{
    public function created($translate)
    {
        $change = json_decode(json_encode($translate), true);

        TranslateJob::dispatch($change, $translate);
    }

    public function updated($translate)
    {
        $change = $translate->getChanges();

        TranslateJob::dispatch($change, $translate);
    }

    public function deleted($translate)
    {
        //
        dd(132132, $translate);
    }

    public function restored($translate)
    {
        //
        dd(132132, $translate);
    }

    public function forceDeleted($translate)
    {
        //
        dd(132132, $translate);
    }
}

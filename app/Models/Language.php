<?php

namespace App\Models;

use App\Models\Translate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Language extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(
            function (Language $language) {
                $table_name = (new Translate())->getTable();
                Language::each(
                    function (Language $language) use ($table_name) {
                        $is_created = collect(DB::select("SHOW COLUMNS FROM ". $table_name))
                            ->where('Field', $language->id)
                            ->count();

                        if ($is_created < 1) {
                            Schema::table($table_name, function (Blueprint $table) use ($language) {
                                $table->string('0'. $language->id)
                                    ->nullable()
                                    ->comment($language->name . " - " . $language->description);
                            });
                        }
                    }
                );

                \App\Models\Access::create(
                    [
                        'name'   => "$language->description [$language->name] show",
                        'entity' => \App\Models\Translate::class,
                        'data'   => ['show','0'.$language->id],
                    ]
                );
                \App\Models\Access::create(
                    [
                        'name'   => "$language->description [$language->name] update",
                        'entity' => \App\Models\Translate::class,
                        'data'   => ['update','0'.$language->id],
                    ]
                );

            }
        );
    }
}

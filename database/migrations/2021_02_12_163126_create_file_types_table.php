<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'file_types',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('class_coder');
                $table->timestamps();
                $table->softDeletes();
            }
        );

        \App\Models\FileType::create(
            [
                'name'        => 'PHP',
                'class_coder' => \App\classes\FileCoder\PhpFileCoder::class,
            ]
        );
        \App\Models\FileType::create(
            [
                'name'        => 'JS',
                'class_coder' => \App\classes\FileCoder\JsFileCoder::class,
            ]
        );
        \App\Models\FileType::create(
            [
                'name'        => 'JSON',
                'class_coder' => \App\classes\FileCoder\JSONFileCoder::class,
            ]
        );
        \App\Models\FileType::create(
            [
                'name'        => 'ENV',
                'class_coder' => \App\classes\FileCoder\ENVFileCoder::class,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_types');
    }
}

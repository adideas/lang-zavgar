<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->integer('file_id')->comment('Файл');
            $table->string('name')->comment('Название');
            $table->string('description')->comment('Описание ключа');
            $table->json('indexed')->comment('Для горячей замены нужно хранить индексацию файла');
            $table->integer('parent')->comment('От чего зависит')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keys');
    }
}

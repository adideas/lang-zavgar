<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_translates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('Пользователь');
            $table->integer('language_id')->comment('Язык');
            $table->integer('key_id')->comment('Ключ');
            $table->integer('file_id')->comment('Файл');
            $table->string('old')->comment('До перевода')->nullable();
            $table->string('new')->comment('После перевода')->nullable();
            $table->integer('count_symbol_with_space')->comment('Количество символов с пробелом')->nullable();
            $table->integer('count_symbol_without_space')->comment('Количество символов без пробелов и знаками препинания')->nullable();
            $table->integer('count_symbol_with_space')->comment('Количество новых символов с пробелом')->nullable();
            $table->integer('count_symbol_without_space')->comment('Количество новых символов без пробелов и знаками препинания')->nullable();
            $table->date('date')->comment('Дата когда делал')->nullable();
            $table->text('html')->comment('Изменения')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_translates');
    }
}

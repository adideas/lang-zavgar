<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('Название');
            $table->string('description')->nullable()->comment('Описание');
            $table->text('path')->comment('Нахождение')->nullable();
            $table->boolean('is_file')->comment('Это файл')->default(0);
            $table->integer('parent')->nullable()->comment('Для связи с главным');
            $table->integer('file_type')->nullable()->comment('Тип файла');
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
        Schema::dropIfExists('files');
    }
}

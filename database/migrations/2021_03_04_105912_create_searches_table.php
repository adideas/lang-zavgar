<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id();
            $table->text('searchable')->nullable()->comment('Строка поиска');
            $table->string('entity')->nullable()->comment('Сущность');
            $table->integer('entity_id')->nullable()->comment('Сущность');
            $table->string('icon_class')->nullable()->comment('SVG');
            $table->timestamps();
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `searches` ADD FULLTEXT search(`searchable`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
}

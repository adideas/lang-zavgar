<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_access', function (Blueprint $table) {
            $table->integer('user_id')->comment('Пользователь');
            $table->string('entity')->comment('Название класса политики');
            $table->string('name')->comment('Название разрешения');
            $table->timestamps();
        });
        Schema::table('user', function (Blueprint $table) {
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
        Schema::dropIfExists('user_access');
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}

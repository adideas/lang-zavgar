<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldListDataInUserAccesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accesses', function (Blueprint $table) {
            $table->json('data')->after('name')->nullable()->comment('Доп информация');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_accesses', function (Blueprint $table) {
            //
            $table->dropColumn('data');
        });
    }
}

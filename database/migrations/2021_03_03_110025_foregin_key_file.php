<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeginKeyFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::unprepared("
            ALTER TABLE `translates`
        CHANGE COLUMN `file_id` `file_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' AFTER `key_id`,
        ADD CONSTRAINT `translates_files_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE;

        ALTER TABLE `keys`
	CHANGE COLUMN `file_id` `file_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Файл' AFTER `id`,
	ADD CONSTRAINT `files_file_id_foregin` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE;

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

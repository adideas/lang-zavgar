<?php

use App\Models\File;
use App\Models\Language;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldListInUserAccesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'user_accesses',
            function (Blueprint $table) {
                $table->dropColumn('entity');
                $table->dropColumn('name');
                $table->dropColumn('data');
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
                $table->integer('access_id');
            }
        );

        \Illuminate\Support\Facades\DB::unprepared(
            "ALTER TABLE `user_accesses`
            CHANGE COLUMN `user_id` `user_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Пользователь' FIRST;

            ALTER TABLE `user_accesses`
            ALTER `access_id` DROP DEFAULT;
        ALTER TABLE `user_accesses`
            CHANGE COLUMN `access_id` `access_id` INT(11) NOT NULL COMMENT 'Доступ' AFTER `user_id`;

        ALTER TABLE `user_accesses`
            ADD CONSTRAINT `users_user_id_foregin` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

            "
        );

        Schema::create(
            'accesses',
            function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable()->comment('Название политики');
                $table->string('entity')->nullable()->comment('Класс');
                $table->json('data')->nullable()->comment('дополнительная информация');
                $table->timestamps();
                $table->softDeletes();
            }
        );

        \Illuminate\Support\Facades\DB::unprepared(
            "ALTER TABLE `user_accesses`
            CHANGE COLUMN `access_id` `access_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Доступ' AFTER `user_id`;
            TRUNCATE `user_accesses`;

        ALTER TABLE `user_accesses`
            ADD CONSTRAINT `accesses_access_id_foregin` FOREIGN KEY (`access_id`) REFERENCES `accesses` (`id`) ON DELETE CASCADE;

ALTER TABLE `oauth_refresh_tokens`
	ADD CONSTRAINT `FK_oauth_refresh_tokens_oauth_access_tokens` FOREIGN KEY (`access_token_id`) REFERENCES `oauth_access_tokens` (`id`) ON DELETE CASCADE;


            "
        );

        \App\Models\Access::create(
            [
                'name'   => 'Полный доступ (root)',
                'entity' => User::class,
                'data'   => ['root'],
            ]
        );

        \App\Models\Access::create(
            [
                'name'   => 'Создание файлов',
                'entity' => File::class,
                'data'   => ['store'],
            ]
        );
        \App\Models\Access::create(
            [
                'name'   => 'Редактирование файлов',
                'entity' => File::class,
                'data'   => ['update'],
            ]
        );
        \App\Models\Access::create(
            [
                'name'   => 'Удаление файлов',
                'entity' => File::class,
                'data'   => ['destroy'],
            ]
        );

        \App\Models\Access::create(
            [
                'name'   => 'Создание пользователей',
                'entity' => User::class,
                'data'   => ['store'],
            ]
        );
        \App\Models\Access::create(
            [
                'name'   => 'Редактирование пользователей',
                'entity' => User::class,
                'data'   => ['update'],
            ]
        );
        \App\Models\Access::create(
            [
                'name'   => 'Удаление пользователей',
                'entity' => User::class,
                'data'   => ['destroy'],
            ]
        );

        \App\Models\Access::create(
            [
                'name'   => 'Добавление языка',
                'entity' => Language::class,
                'data'   => ['store'],
            ]
        );
        \App\Models\Access::create(
            [
                'name'   => 'Редактирование языка',
                'entity' => Language::class,
                'data'   => ['update'],
            ]
        );
        \App\Models\Access::create(
            [
                'name'   => 'Удаление языка',
                'entity' => Language::class,
                'data'   => ['destroy'],
            ]
        );

        Language::each(
            function (Language $language) {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LaravelInit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \Illuminate\Support\Facades\DB::unprepared(
            "
            INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
                (1, NULL, 'Laravel Personal Access Client', 'LgFdqKuTrTx6hvZ3rag6Vjbf3venSoGbMqSTIMj0', NULL, 'http://localhost', 1, 0, 0, '2021-02-12 09:59:18', '2021-02-12 09:59:18'),
                (2, NULL, 'Laravel Password Grant Client', 'aNAx2J00jVuegapEjbbN6L9Bnmjoqk0kOWuQjJ9F', 'users', 'http://localhost', 0, 1, 0, '2021-02-12 09:59:18', '2021-02-12 09:59:18');
            INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
                (1, 1, '2021-02-12 09:59:18', '2021-02-12 09:59:18');
            "
        );


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

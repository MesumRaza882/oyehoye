<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('settings', function (Blueprint $table) {
        //     $table->id();

        //     $table->timestamps();
        // });
        DB::statement('CREATE TABLE `settings` (
            `id` int(10) UNSIGNED NOT NULL,
            `attribute` varchar(255) NOT NULL,
            `label` varchar(255) DEFAULT NULL,
            `value` text DEFAULT NULL,
            `possible_values` text DEFAULT NULL COMMENT \'In case of select/check/radio this filed is required.\r\nIt is comma seprated without space\',
            `type` varchar(32) NOT NULL DEFAULT \'text\',
            `route` varchar(255) NOT NULL,
            `sort` tinyint(2) DEFAULT NULL,
            `is_hidden` tinyint(1) NOT NULL DEFAULT 0
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;');
        DB::statement("ALTER TABLE `settings` ADD PRIMARY KEY (`id`);");
        DB::statement("ALTER TABLE `settings` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}

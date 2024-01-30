<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable();
            $table->string('username')->nullable();
            $table->string('wa')->nullable();
            $table->string('image')->nullable();
            $table->string('token')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('username');
            $table->dropColumn('wa');
            $table->dropColumn('image');
            $table->dropColumn('token');
        });
    }
}

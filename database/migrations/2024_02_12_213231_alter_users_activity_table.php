<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersActivityTable extends Migration
{
    public function up()
    {
        Schema::table('users_activity', function (Blueprint $table) {
            $table->ipAddress('ip_address')->nullable();
            $table->string('device_info')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users_activity', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'device_info']);
        });
    }
}

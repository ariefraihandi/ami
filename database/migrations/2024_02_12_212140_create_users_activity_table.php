<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersActivityTable extends Migration
{
    public function up()
    {
        Schema::create('users_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('activity');
            $table->timestamp('created_at')->useCurrent();
            // Tambahkan kolom lainnya di sini jika diperlukan
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_activity');
    }
}

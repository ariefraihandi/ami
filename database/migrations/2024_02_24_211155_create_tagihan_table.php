<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagihanTable extends Migration
{
    public function up()
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tagihan');
            $table->string('jenis_tagihan');
            $table->decimal('jumlah_tagihan', 10, 2);
            $table->date('start_tagihan');
            $table->unsignedInteger('tagihan_ke')->nullable();
            $table->unsignedInteger('sampai_ke')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihan');
    }
}

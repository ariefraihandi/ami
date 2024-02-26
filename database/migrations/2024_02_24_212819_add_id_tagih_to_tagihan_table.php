<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdTagihToTagihanTable extends Migration
{
    public function up()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tagih')->after('id');
            $table->string('masa_kerja')->after('jumlah_tagihan');
            $table->string('status')->after('sampai_ke');
        });
    }

    public function down()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('id_tagih');
        });
    }
}

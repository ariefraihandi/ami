<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahKolomKeTbItemInvoices extends Migration
{
    public function up()
    {
        Schema::table('item_invoices', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->string('ukurana')->nullable();
            $table->string('ukuranb')->nullable();
            $table->string('bulata')->nullable();
            $table->string('bulatb')->nullable();
            $table->string('sales')->nullable();
        });
    }

    public function down()
    {
        Schema::table('item_invoices', function (Blueprint $table) {
            // Rollback jika diperlukan
            $table->dropColumn('ukurana');
            $table->dropColumn('ukuranb');
            $table->dropColumn('bulata');
            $table->dropColumn('bulatb');
            $table->dropColumn('sales');
        });
    }
};
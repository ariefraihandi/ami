<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToItemInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_invoices', function (Blueprint $table) {
            $table->string('discount')->default('0');
            $table->string('tax')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_invoices', function (Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('tax');
        });
    }
}
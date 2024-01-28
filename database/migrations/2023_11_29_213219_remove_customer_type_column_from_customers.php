<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCustomerTypeColumnFromCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove the customer_type column
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If needed, you can add code here to recreate the customer_type column
        // However, dropping a column is usually irreversible, so this method may be empty
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateCustomerTypeColumnInCustomers extends Migration
{
    public function up()
    {
        // Recreate the customer_type column with new enum values
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('customer_type', ['individual', 'biro', 'instansi'])
                ->default('individual')
                ->after('address');
        });
    }

    public function down()
    {
        // Drop the recreated customer_type column
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });
    }
};
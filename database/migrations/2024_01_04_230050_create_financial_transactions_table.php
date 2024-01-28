<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('source_receiver');
            $table->string('description');
            $table->decimal('transaction_amount', 10, 2); // Using decimal for money values
            $table->string('payment_method');
            $table->string('reference_number');
            $table->integer('status'); // 1 for income, 2 for expense
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_transactions');
    }
};
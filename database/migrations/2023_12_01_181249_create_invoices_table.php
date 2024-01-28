<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('customer_uuid')->references('uuid')->on('customers');
            $table->string('invoice_number')->unique();
            $table->string('invoice_name');
            $table->string('type');
            $table->string('status')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->timestamp('due_date')->nullable();
            $table->text('additional_notes')->nullable();
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
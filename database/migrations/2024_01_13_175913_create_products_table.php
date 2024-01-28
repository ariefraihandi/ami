<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kode')->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('harga_beli', 10, 2)->nullable();
            $table->decimal('harga_jual_individu', 10, 2)->nullable();
            $table->decimal('harga_jual_biro', 10, 2)->nullable();
            $table->decimal('harga_jual_instansi', 10, 2)->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
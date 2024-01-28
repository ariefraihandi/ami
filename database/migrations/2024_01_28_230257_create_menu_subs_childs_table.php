<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuSubsChildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_subs_childs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_submenu');
            $table->string('title');
            $table->unsignedInteger('order');
            $table->string('url');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            // Menambahkan indeks ke kolom id_submenu
            $table->index('id_submenu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_subs_childs');
    }
}

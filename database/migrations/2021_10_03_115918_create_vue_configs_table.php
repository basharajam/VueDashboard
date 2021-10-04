<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVueConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vue_configs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String('name');
            $table->String('key');
            $table->String('value');
            $table->String('subValue');
            $table->String('type');
            $table->String('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vue_configs');
    }
}
